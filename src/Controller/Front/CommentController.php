<?php

namespace App\Controller\Front;

use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/front/delete/comment/{id}", name="front_delete_comment")
     */
    public function deleteComment(
        $id,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManagerInterface
    ) {
        $comment = $commentRepository->find($id);

        $entityManagerInterface->remove($comment);
        $entityManagerInterface->flush();

        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("front/update/comment/{id}", name="front_update_comment")
     */
    public function updateCommit(
        $id,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {

        $comment = $commentRepository->find($id);

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);



        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("app_login");
        }

        return $this->render("front/commentform.html.twig", ['commentForm' => $commentForm->createView()]);
    }

    /**
     * @Route("delete/comment/{id}", name="delete_comment")
     */
    public function commentDelete(
        $id,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManagerInterface
    ) {

        $comment = $commentRepository->find($id);
        $product = $comment->getProduct();

        $product_id = $product->getId();

        $entityManagerInterface->remove($comment);
        $entityManagerInterface->flush();

        return $this->redirectToRoute("front_show_product", ['id' => $product_id]);
    }

    /**
     * @Route("update/comment/{id}", name="update_comment")
     */
    public function commitUpdate(
        $id,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {

        $comment = $commentRepository->find($id);

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        $product = $comment->getProduct();

        $product_id = $product->getId();



        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("front_show_product", ['id' => $product_id]);
        }

        return $this->render("front/commentform.html.twig", ['commentForm' => $commentForm->createView()]);
    }
}