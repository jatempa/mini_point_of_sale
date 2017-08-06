<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryAPIController extends Controller
{
    /**
     * @Get("/categories")
     */
    public function getCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Category')->findAllCategories();

        $view = View::create()->setData(array('categories' => $categories));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
