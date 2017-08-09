<?php

/**
 * Created by PhpStorm.
 * User: jorge antonio atempa
 * Date: 05/08/17
 * Time: 11:28 PM
 */

namespace AppBundle\Controller;

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

class NoteAPIController extends Controller
{
    /**
     * @Get("/notes/lastNoteId")
     */
    public function getNotesAction()
    {
        $em = $this->getDoctrine()->getManager();
        // Get User Id
        $user_id = $this->getUser();
        $user_id->getId();
        $numberNote = $em->getRepository('AppBundle:Note')->findLastNoteIdByUser($user_id);

        $view = View::create()->setData($numberNote);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Get("/notes/pending")
     */
    public function getPendingNotesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $notes = $em->getRepository('AppBundle:Note')->findAllPendingNotes();

        $view = View::create()->setData(array('notes' => $notes));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @Post("/notes/create")
     */
    public function postCreateNoteAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $result = null;
            // Get data from client
            $selectedAccount = $request->request->get('selectedAccount');
            $numberNote = $request->request->get('numberNote');
            $products = $request->request->get('products');
            // Prepare ORM
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {

                $account = $em->getRepository('AppBundle:Account')->findOneById($selectedAccount);

                $note = new Note();
                $note->setNumberNote($numberNote);
                $note->setStatus("Pendiente");
                $note->setCheckin(new \DateTime('now'));
                $note->setUser($this->getUser());
                $note->setAccount($account);
                $em->persist($note);
                $em->flush();

                foreach ($products as $product) {
                    if (!is_null($product)) {
                        $noteProduct = new NoteProduct();
                        $noteProduct->setNote($note);
                        $prod = $em->getRepository('AppBundle:Product')->findOneById($product['id']);
                        $noteProduct->setProduct($prod);
                        $noteProduct->setAmount($product['amount']);
                        $noteProduct->setTotal($product['amount'] * $product['price']);
                        // Save Product
                        $em->persist($noteProduct);
                        $em->flush();
                    }
                }
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
