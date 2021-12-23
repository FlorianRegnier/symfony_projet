<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/", name="front_home")
     */
    public function home(CategoryRepository $categoryRepository)
    {
        $id = rand(1, 10);
        $categorie = $categoryRepository->find($id);
        if ($categorie) {
            return $this->render('front/home.html.twig', ['category' => $categorie]);
        } else {
            return $this->redirectToRoute('front_home');
        }
    }

    /**
     * @Route("front/search/", name="front_search")
     */
    public function frontSearch(ProductRepository $productRepository, Request $request)
    {
        $term = $request->query->get('term');

        $products = $productRepository->searchByTerm($term);

        return $this->render('front/search.html.twig', ['products' => $products]);
    }
}