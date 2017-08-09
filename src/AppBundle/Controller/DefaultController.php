<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

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
        $ac = $this->get('security.authorization_checker');
        if ($ac->isGranted('ROLE_MESERO') && $ac->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('comandas'));
        }

        return $this->render('default/default.html.twig');
    }

    /**
     * @Route("/cuentas", name="cuentas")
     */
    public function accountsAction(Request $request)
    {
        $ac = $this->get('security.authorization_checker');
        if ($ac->isGranted('ROLE_MESERO') && $ac->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('accounts/index.html.twig');
    }

    /**
     * @Route("/comandas", name="comandas")
     */
    public function notesAction(Request $request)
    {
        $ac = $this->get('security.authorization_checker');
        if ($ac->isGranted('ROLE_MESERO') && $ac->isGranted('ROLE_USER')) {
            return $this->render('notes/index.html.twig');
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/pendientes", name="pendientes")
     */
    public function notesPendingAction(Request $request)
    {
        $ac = $this->get('security.authorization_checker');
        if ($ac->isGranted('ROLE_PALOMA') && $ac->isGranted('ROLE_ADMIN')) {
            return $this->render('notes/show.html.twig');
        }

        return $this->redirectToRoute('homepage');
    }
}
