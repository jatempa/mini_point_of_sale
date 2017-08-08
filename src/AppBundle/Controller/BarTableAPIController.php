<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BarTableAPIController extends Controller
{
    /**
     * @Get("/tables")
     */
    public function getTablesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mesas = $em->getRepository('AppBundle:BarTable')->findAllTables();

        $view = View::create()->setData(array('mesas' => $mesas));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
