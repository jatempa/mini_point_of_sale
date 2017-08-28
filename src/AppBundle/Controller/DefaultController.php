<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('default/default.html.twig');
    }

    /**
     * @Route("/corte", name="corte")
     * @Method("GET")
     */
    public function corteAction()
    {
        $chk = $this->get('security.authorization_checker');
        if ($chk->isGranted('ROLE_CAJERO')) {
            return $this->render('default/corte.html.twig');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/cuentas", name="cuentas")
     * @Method("GET")
     */
    public function accountsAction()
    {
        $chk = $this->get('security.authorization_checker');
        if ($chk->isGranted('ROLE_MESERO') || $chk->isGranted('ROLE_PALOMASHOTS')) {
            return $this->render('accounts/index.html.twig');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/comandas", name="comandas")
     * @Method("GET")
     */
    public function notesAction()
    {
        $chk = $this->get('security.authorization_checker');
        if ($chk->isGranted('ROLE_MESERO') || $chk->isGranted('ROLE_PALOMASHOTS')) {
            return $this->render('notes/index.html.twig');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/pendientes", name="pendientes")
     * @Method("GET")
     */
    public function notesPendingAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_PALOMA')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('notes/show.html.twig');
    }

    /**
     * @Route("/cancelaciones", name="cancelations")
     * @Method("GET")
     */
    public function notesCancelingAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $notes = $em->getRepository('AppBundle:Note')->findUsersWithDeliveredNotes();

        for ($i = 0; $i < count($notes); $i++) {
            $notes[$i]['products'] = $em->getRepository('AppBundle:Note')->findProductsByNote($notes[$i]['userId'], $notes[$i]['numberNote']);
        }

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($notes, $request->query->getInt('page', 1), 10);

        return $this->render(
            'notes/canceling.html.twig',
            array('notes' => $pagination)
        );
    }

    /**
     * @Route("/notes/checkin/product/cancelaciones/{userId}/{folioNumber}", name="cancel_note")
     * @Method("GET")
     */
    public function cancelNoteAction($userId, $folioNumber)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Prepare ORM
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            // Change status of note
            $ps = $em->getRepository('AppBundle:Note')->findProductsByNote($userId, $folioNumber);

            foreach ($ps as $p) {
              // Reduce stock
              $product = $em->getRepository('AppBundle:Product')->findOneById($p['id']);
              $tempStock = $product->getStock();

              $reduceAmount = $tempStock + $p['amount'];
              if ($reduceAmount < 0) {
                $em->getConnection()->rollBack();
              } else {
                $product->setStock($reduceAmount);
                $em->flush();
              }
            }

            $n = $em->getRepository('AppBundle:NoteProduct')->findNoteProductIdForCancel($userId, $folioNumber);
            //Update status of note product
            $note = $em->getRepository('AppBundle:Note')->findOneById($n['id']);
            $note->setStatus('Cancelado');
            $note->setCheckOut(new \DateTime('now'));
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }

        return $this->redirectToRoute('cancelations');
    }
}