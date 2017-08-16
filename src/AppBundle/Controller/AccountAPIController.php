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
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
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
     * @Get("/accounts/date")
     */
    public function getAccountsByDateAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Get User Id
        $userId = $this->getUser();
        $userId->getId();
        $accounts = $em->getRepository('AppBundle:Account')->findAllAccountsByDate($userId);

        $view = View::create()->setData(array("accounts" => $accounts));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Get("/admin/accounts/all")
     */
    public function getAllAccountsByWaiterAdminAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        // Get Users
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
     * @Get("/accounts/all")
     */
    public function getAllAccountsByWaiterAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MESERO')) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isXmlHttpRequest()) {
            $result = null;

            try {
                // Get User Id
                $userId = $this->getUser();
                $userId->getId();
                $user = Array();

                $em = $this->getDoctrine()->getManager();
                $user['accounts'] = $em->getRepository('AppBundle:Account')->findAllAccounts($userId);
                $subtotal = 0;
                $servicio = 0;
                $total = 0;
                $total_general = 0;

                $connector = new FilePrintConnector("/dev/usb/lp0");
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("REPUBLIK\n");
                $printer->text("Live Music");
                $printer->feed(2);
                $printer->text("Mesero(a)" . $this->getUser() . "\n");
                $printer->feed(2);
                for ($j = 0; $j < count($user['accounts']); $j++) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $user['accounts'][$j]['notes'] = $em->getRepository('AppBundle:Account')->findAccountByUserIdAndTableId($user['accounts'][$j]['id'], $userId);
                    $printer->text("Mesa " . $user['accounts'][$j]['mesaid'] . "\n");
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->text(str_pad("Cantidad", 9));
                    $printer->text(str_pad("  Producto", 23));
                    $printer->text(str_pad("Total", 10,' ', STR_PAD_LEFT));
                    $printer->text(str_pad("_", 42,'_'));
                    $printer->text("\n");
                    for ($k = 0; $k < count($user['accounts'][$j]['notes']); $k++) {
                        $user['accounts'][$j]['notes'][$k]['products'] = $em->getRepository('AppBundle:Note')->findProductsByNote($userId, $user['accounts'][$j]['notes'][$k]['numberNote']);
                        for ($l = 0; $l < count($user['accounts'][$j]['notes'][$k]['products']); $l++) {
                            if ($user['accounts'][$j]['notes'][$k]['products'][$l]['price'] > 0) {
                                $printer->text(str_pad($user['accounts'][$j]['notes'][$k]['products'][$l]['amount'], 9,' ', STR_PAD_LEFT));
                                $printer->text(str_pad('  ' . utf8_decode($user['accounts'][$j]['notes'][$k]['products'][$l]['product']), 23));
                                $printer->text(str_pad(number_format($user['accounts'][$j]['notes'][$k]['products'][$l]['amount'] * $user['accounts'][$j]['notes'][$k]['products'][$l]['price'], 2, '.', ','), 10, ' ', STR_PAD_LEFT));
                                $printer->text("\n");
                                $subtotal += $user['accounts'][$j]['notes'][$k]['products'][$l]['amount'] * $user['accounts'][$j]['notes'][$k]['products'][$l]['price'];
                            }
                        }
                    }
                }
                $printer->text(str_pad("_", 42,'_'));
                $printer->text(str_pad("Subtotal $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($subtotal,2, '.', ','),10,' ',STR_PAD_LEFT));
                $servicio = $subtotal * 0.05;
                $printer->text(str_pad("Piso 5% $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($servicio,2, '.', ','),10,' ',STR_PAD_LEFT));
                $total = $subtotal + $servicio;
                $printer->text(str_pad("Total $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($total,2, '.', ','),10,' ',STR_PAD_LEFT));
                $printer->feed(2);
                $total_general += $total;
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text(str_pad("Total a pagar $", 20,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($total_general,2, '.', ','),10,' ',STR_PAD_LEFT));
                $printer->feed(2);
                $printer->cut();
                $printer->cloe();
                $result = "success";
            } catch (Exception $e) {
                throw $e;
            }

            return new JsonResponse($result);
        }

        return new Response('This is not ajax!', 400);
    }

    /**
     * @Get("/accounts/{accountId}")
     */
    public function getAccountsByAccountIdAction(Request $request, $accountId)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MESERO')) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isXmlHttpRequest()) {
            $result = null;

            try {
                $em = $this->getDoctrine()->getManager();
                // Get User Id
                $userId = $this->getUser();
                $userId->getId();
                $accounts = $em->getRepository('AppBundle:Account')->findAccountByUserIdAndTableId($accountId, $userId);
                $subtotal = 0;
                $servicio = 0;
                $total = 0;
                $connector = new FilePrintConnector("/dev/usb/lp0");
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("REPUBLIK\n");
                $printer->text("Live Music");
                $printer->feed(2);
                $printer->text("Mesa " . $accounts[0]['id'] . "\n");
                $printer->text("Mesero(a)" . $accounts[0]['waiter'] . "\n");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text(str_pad("Cantidad", 9));
                $printer->text(str_pad("  Producto", 23));
                $printer->text(str_pad("Total", 10,' ', STR_PAD_LEFT));
                $printer->text(str_pad("_", 42,'_'));
                $printer->text("\n");
                for ($i = 0; $i < count($accounts); $i++) {
                    $accounts[$i]['products'] = $em->getRepository('AppBundle:Note')->findProductsByNote($userId, $accounts[$i]['numberNote']);
                    for ($j = 0; $j < count($accounts[$i]['products']); $j++) {
                        if ($accounts[$i]['products'][$j]['price'] > 0) {
                            $printer->text(str_pad($accounts[$i]['products'][$j]['amount'], 9,' ', STR_PAD_LEFT));
                            $printer->text(str_pad('  ' . utf8_decode($accounts[$i]['products'][$j]['product']), 23));
                            $printer->text(str_pad(number_format($accounts[$i]['products'][$j]['amount'] * $accounts[$i]['products'][$j]['price'], 2, '.', ','), 10, ' ', STR_PAD_LEFT));
                            $printer->text("\n");
                            $subtotal += $accounts[$i]['products'][$j]['amount'] * $accounts[$i]['products'][$j]['price'];
                        }
                    }
                }
                $printer->text(str_pad("_", 42,'_'));
                $printer->text(str_pad("Subtotal $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($subtotal,2, '.', ','),10,' ',STR_PAD_LEFT));
                $servicio = $subtotal * 0.10;
                $printer->text(str_pad("Propina y servicio 10% $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($servicio,2, '.', ','),10,' ',STR_PAD_LEFT));
                $total = $subtotal + $servicio;
                $printer->text(str_pad("Total $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($total,2, '.', ','),10,' ',STR_PAD_LEFT));
                $printer->feed(2);
                $printer->cut();
                $printer->cloe();
                $result = "success";
            } catch (Exception $e) {
                throw $e;
            }

            return new JsonResponse($result);
        }

        return new Response('This is not ajax!', 400);
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
                $account = $em->getRepository('AppBundle:Account')->findOneById($accountId);
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
