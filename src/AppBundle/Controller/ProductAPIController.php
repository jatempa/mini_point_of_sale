<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductAPIController extends Controller
{
    /**
     * @Get("/products")
     */
    public function getProductsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('AppBundle:Product')->findAllProducts();

        $view = View::create()->setData(array('products' => $products));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
