<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
        return $this->render('default/default.html.twig');
    }

    /**
     * @Route("/cuentas", name="cuentas")
     * @Cache(maxage="300")
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
     * @Cache(maxage="300")
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
}