<?php

namespace App\Controller\Front;

use App\Repository\ProductRepository;
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
    public function showProduct($id, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);

        return $this->render('front/product.html.twig', ['product' => $product]);
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