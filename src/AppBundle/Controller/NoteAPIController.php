<?php

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
     * @Post("/notes/create")
     */
    public function postRegistroEquipoAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $result = null;

            $noteData = $request->request->all();
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction(); // suspend auto-commit
            try {
                $note = new Note();
                $note->setNumberNote($noteData['numberNote']);
                $note->setStatus("Pendiente");
                $note->setCheckin(new \DateTime('now'));
                $note->setUser($this->getUser());
                $em->persist($note);
                $em->flush();

                $noteProduct = new NoteProduct();
                $noteProduct->setNote($note);
                $product = $em->getRepository('AppBundle:Product')->findOneById($noteData['product']);
                if(!is_null($product)) {
                    $noteProduct->setProduct($product);
                }
                $noteProduct->setAmount($noteData['amount']);
                $noteProduct->setTotal($noteData['total']);
                // Save Note
                $em->persist($noteProduct);
                $em->flush();
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
