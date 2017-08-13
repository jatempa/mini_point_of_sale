<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountAPIController extends Controller
{
    /**
     * @Get("/accounts")
     */
    public function getAccountsAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Get User Id
        $userId = $this->getUser();
        $userId->getId();
        $accounts = $em->getRepository('AppBundle:Account')->findAllAccounts($userId);

        $view = View::create()->setData(array("accounts" => $accounts));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Get("/accounts/all")
     */
    public function getAllAccountsByWaiterAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Get User Id
        $users = $em->getRepository('AppBundle:User')->findWaitersId();

        for ($i = 0; $i < count($users); $i++) {
            $users[$i]['accounts'] = $em->getRepository('AppBundle:Account')->findAllAccounts($users[$i]['id']);
            for ($j = 0; $j < count($users[$i]['accounts']); $j++) {
                $users[$i]['accounts'][$j]['notes'] = $em->getRepository('AppBundle:Account')->findAccountByUserIdAndTableId($users[$i]['accounts'][$j]['id'], $users[$i]['id']);
                for ($k = 0; $k < count($users[$i]['accounts'][$j]['notes']); $k++) {
                    $users[$i]['accounts'][$j]['notes'][$k]['products'] = $em->getRepository('AppBundle:Note')->findProductsByNote($users[$i]['id'], $users[$i]['accounts'][$j]['notes'][$k]['numberNote']);
                }
            }
        }

        $view = View::create()->setData(array("users" => $users));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Get("/accounts/{accountId}")
     */
    public function getAccountsByAccountIdAction($accountId, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        // Get User Id
        $userId = $this->getUser();
        $userId->getId();
        $accounts = $em->getRepository('AppBundle:Account')->findAccountByUserIdAndTableId($accountId, $userId);

        for ($i = 0; $i < count($accounts); $i++) {
            $accounts[$i]['products'] = $em->getRepository('AppBundle:Note')->findProductsByNote($userId, $accounts[$i]['numberNote']);
        }

        $view = View::create()->setData(array("accounts" => $accounts));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Get("/accounts/{accountId}/{userId}")
     */
    public function getAccountsByAccountIdAndUserIdAction($accountId, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository('AppBundle:Account')->findAccountByUserIdAndTableId($accountId, $userId);

        for ($i = 0; $i < count($accounts); $i++) {
            $accounts[$i]['products'] = $em->getRepository('AppBundle:Note')->findProductsByNote($userId, $accounts[$i]['numberNote']);
        }

        $view = View::create()->setData(array("accounts" => $accounts));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Post("/accounts/create")
     */
    public function postCreateAccountAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $result = null;
            // Get data from client
            $selectedTable = $request->request->get('selectedTable');
            // Prepare ORM
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $account = new Account();
                $tableNumber = $em->getRepository('AppBundle:BarTable')->findOneById($selectedTable);
                $account->setCheckin(new \DateTime('now'));
                $account->setBarTable($tableNumber);
                $account->setUser($this->getUser());
                $account->setStatus(true);

                $em->persist($account);
                $em->flush();

                $em->getConnection()->commit();
                $result = "success";
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }

            return new JsonResponse($result);
        }

        return new Response('This is not ajax!', 400);
    }

    /**
     * @Put("/accounts/close")
     */
    public function putCloseAccountAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $result = null;
            // Get data from client
            $accountId = $request->request->get('id');
            $userId = $this->getUser()->getId();
            $selectedTable = $request->request->get('mesaId');
            // Prepare ORM
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $account = $em->getRepository('AppBundle:Account')
                              ->findAccountByUserIdAndTableId($accountId, $userId);
                $account->setCheckout(new \DateTime('now'));
                $account->setStatus(false);
                $em->flush();

                $em->getConnection()->commit();
                $result = "success";
            } catch (Exception $e) {
                $em->getConnection()->rollBack();
                throw $e;
            }

            return new JsonResponse($result);
        }

        return new Response('This is not ajax!', 400);
    }
}
