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
        $user_id = $this->getUser();
        $user_id->getId();
        $accounts = $em->getRepository('AppBundle:Account')->findAllAccounts($user_id);

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