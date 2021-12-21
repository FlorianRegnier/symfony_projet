<?php

namespace App\Controller\Front;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{

    /**
     * @Route("front/products/", name="front_list_product")
     */
    public function listProduct(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('front/products.html.twig', ['products' => $products]);
    }


    
   /**
     * @Route("front/product/{id}", name="front_show_product")
     */
    public function showProduct($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $product = $productRepository->find($id);

        $user = $this->getUser();

        if($user)
        {
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findBy(['email' => $user_mail]);
        }

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if($commentForm->isSubmitted() && $commentForm->isValid()){
            $comment->setDate(new \DateTime("NOW"));
            $comment->setProduct($productRepository->find($id));
            $comment->setUser($user_true[0]);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('front_show_product', ['id' => $id]);
        }

        return $this->render("front/product.html.twig", ['product' => $product,
            'commentForm' => $commentForm->createView()]);
            
    }










   


    /**
     * @Route("front/search/", name="front_search")
     */
    public function frontSearch(ProductRepository $productRepository, Request $request)
    {
        //recuperer els donnes du tableau
        $term = $request->query->get('term');// query car le form est en get.
                                            // si form en post alors use request au lieu de query

        $products = $productRepository->searchByTerm($term);
        
        
        return $this->render('front/search.html.twig', ['products' => $products]);
    }




    

}