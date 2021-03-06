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
     * @Get("/accounts/{accountId}")
     */
    public function getAccountsByAccountIdAction($accountId)
    {
        $chk = $this->get('security.authorization_checker');
        if ($chk->isGranted('ROLE_MESERO') || $chk->isGranted('ROLE_PALOMASHOTS')) {
            try {
                $em = $this->getDoctrine()->getManager();
                // Get User Id
                $userId = $this->getUser();
                $userId->getId();
                $accounts = $em->getRepository('AppBundle:Account')->findAccountByUserId($accountId, $userId);
                $subtotal = 0;

                $connector = new FilePrintConnector("/dev/usb/lp0");
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("REPUBLIK\n");
                $printer->text("Live Music");
                $printer->feed(2);
                $printer->text("Mesero(a) " . $userId->getName() . " " . $userId->getFirstLastName() . "\n");
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
                $printer->text(str_pad("Prop. y Servicio Sugerido 10% $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($servicio,2, '.', ','),10,' ',STR_PAD_LEFT));
                $total = $subtotal + $servicio;
                $printer->text(str_pad("Total $", 32,' ', STR_PAD_LEFT));
                $printer->text(str_pad(number_format($total,2, '.', ','),10,' ',STR_PAD_LEFT));
                $printer->feed(2);
                $printer->cut();
                $printer->close();
            } catch (Exception $e) {
                throw $e;
            }
        }

        $view = View::create()->setData(array("accounts" => $accounts));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Get("/accounts/{accountId}/details")
     */
    public function getDetailsByAccountIdAction($accountId)
    {
        $chk = $this->get('security.authorization_checker');
        // initialize variable
        $accounts = null;

        if ($chk->isGranted('ROLE_MESERO') || $chk->isGranted('ROLE_PALOMASHOTS')) {
            $em = $this->getDoctrine()->getManager();
            // Get User Id
            $userId = $this->getUser();
            $userId->getId();
            $account = $em->getRepository('AppBundle:Account')->findDetailsByAccountId($accountId, $userId);
        }

        $view = View::create()->setData(array("account" => $account));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Get("/accounts/{accountId}/{userId}")
     */
    public function getAccountsByAccountIdAndUserIdAction($accountId, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository('AppBundle:Account')->findAccountByUserId($accountId, $userId);

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
            $accountName = $request->request->get('name');
            // Prepare ORM
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $account = new Account();
                if (!is_null($accountName)) {
                    $account->setName($accountName);
                }
                $account->setCheckin(new \DateTime('now'));
                $account->setUser($this->getUser());

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
}
