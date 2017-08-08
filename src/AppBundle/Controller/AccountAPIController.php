<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Note;
use AppBundle\Entity\NoteProduct;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Exception\Exception;

class AccountAPIController extends Controller
{
    /**
     * @Post("/accounts/create")
     */
    public function postCreateAccountAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $result = null;
            // Get data from client
//            $selectedTable = $request->request->get('selectedTable');
            // Prepare ORM
//            $em = $this->getDoctrine()->getManager();
//            $em->getConnection()->beginTransaction(); // suspend auto-commit
//            try {
//
//
//                $em->getConnection()->commit();
                $result = "success";
//            } catch (Exception $e) {
//                $em->getConnection()->rollBack();
//                throw $e;
//            }

            return new JsonResponse($result);
        }

        return new Response('This is not ajax!', 400);
    }
}
