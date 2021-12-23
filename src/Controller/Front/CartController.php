<?php

namespace App\Controller\Front;

use App\Entity\Command;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("front/cart/add/{id}", name="add_cart")
     */
    public function addCart($id, SessionInterface $sessionInterface)
    {
        $cart = $sessionInterface->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute('front_show_product', ['id' => $id]);
    }

    /**
     * @Route("front/cart/", name="show_cart")
     */
    public function showCart(
        SessionInterface $sessionInterface,
        ProductRepository $productRepository
    ) {
        $cart = $sessionInterface->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $this->render('front/cart.html.twig', ['items' => $cartWithData]);
    }

    /**
     * @Route("/cart/delete/{id}", name="delete_cart")
     */
    public function deleteCart($id, SessionInterface $sessionInterface)
    {
        $cart = $sessionInterface->get('cart', []);

        if (!empty($cart[$id] && $cart[$id] == 1)) {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }

        $sessionInterface->set('cart', $cart);

        return $this->redirectToRoute('show_cart');
    }

    /**
     * @Route("front/cart/infos", name="cart_infos")
     */
    public function infosCart(UserRepository $userRepository)
    {
        $user = $this->getUser();

        if ($user) {
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findOneBy(['email' => $user_mail]);

            return $this->render("front/infoscart.html.twig", ['user' => $user_true]);
        } else {
            return $this->render("front/infoscart.html.twig");
        }
    }

    /**
     * @Route("/front/command/create", name="command_create")
     */
    public function commandCreate(
        SessionInterface $sessionInterface,
        ProductRepository $productRepository,
        UserRepository $userRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {
        $command = new Command();

        $id = $command->getId();

        $command->setNumber("Command-" . $id);
        $command->setDate(new \DateTime("NOW"));

        $cart = $sessionInterface->get('cart', []);
        $price = 0;

        foreach ($cart as $id_product => $quantity) {
            $product = $productRepository->find($id_product);
            $price_product = $product->getPrice();
            $price = $price + ($price_product * $quantity);
            unset($cart[$id_product]);
            $sessionInterface->set('cart', $cart);
            $command->addProduct($product);
        }

        $command->setPrice($price);

        $user = $this->getUser();

        if ($user) {
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findOneBy(['email' => $user_mail]);

            $command->setUser($user_true);
        } else {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $adress = $request->request->get('adress');
            $city = $request->request->get('city');
            $zipcode = $request->request->get('zipcode');

            $command->setName($name);
            $command->setEmail($email);
            $command->setAdress($adress);
            $command->setCity($city);
            $command->setZipcode($zipcode);
        }

        $entityManagerInterface->persist($command);
        $entityManagerInterface->flush();

        return $this->redirectToRoute("front_home");
    }
}