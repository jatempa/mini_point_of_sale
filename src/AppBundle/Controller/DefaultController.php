<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /*
        $connector = new CupsPrintConnector("BIXOLON-SRP-330II");
        $printer = new Printer($connector);
        $printer -> text("Hello World!\n");
        $printer -> text("Jorge Atempa!\n");
        $printer -> cut();
        $printer -> cloe();*/

        return $this->render('default/default.html.twig');
    }

    /**
     * @Route("/cuentas", name="cuentas")
     * @Method("GET")
     */
    public function accountsAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MESERO')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('accounts/index.html.twig');
    }

    /**
     * @Route("/comandas", name="comandas")
     * @Method("GET")
     */
    public function notesAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_MESERO')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('notes/index.html.twig');
    }

    /**
     * @Route("/pendientes", name="pendientes")
     */
    public function notesPendingAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_PALOMA')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('notes/show.html.twig');
    }

    /**
     * @Route("/cancelaciones", name="cancelaciones")
     */
    public function notesCancelingAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $notes = $em->getRepository('AppBundle:Note')->findUsersWithDeliveredNotes();

        for ($i = 0; $i < count($notes); $i++) {
            $notes[$i]['products'] = $em->getRepository('AppBundle:Note')->findDeliveredNoteProducts($notes[$i]['userId'], $notes[$i]['numberNote']);
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($notes, $request->query->getInt('page', 1), 10);

        return $this->render(
            'notes/canceling.html.twig',
            array('notes' => $pagination)
        );
    }
}