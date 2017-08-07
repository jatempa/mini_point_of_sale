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

    /**
     * @Get("/products/{id}")
     */
    public function getProductByIdAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('AppBundle:Product')->findProductById($id);

        $view = View::create()->setData(array('product' => $product));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
