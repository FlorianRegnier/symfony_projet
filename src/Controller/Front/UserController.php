<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("front/user/add/", name="add_user")
     */
      public function addUser(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $userPasswordHasherInterface)
      {
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            $user->setRoles(["ROLE_USER"]);
            $user->setDate(new \DateTime("NOW"));


            // on recup le password entre ds le form
            $plainPassword = $userForm->get('password')->getData();
            // on hash le password pour le securisé
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("app_login");

        }

        return $this->render('front/userform.html.twig', ['userForm' => $userForm->createView()]);

      }
        




   /**
     * @Route("front/user/update/{id}", name="update_user")
     */                                       
    public function updateuser($id, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, Request $request, UserPasswordHasherInterface $userPasswordHasherInterface )
    {
       $user = $userRepository->find($id);
       
       /*
        plus safe pour secure
       $user = $this->getUser();
       */
       
       $userForm = $this->createForm(UserType::class, $user);
        
       $userForm->handleRequest($request);

       if($userForm->isSubmitted() && $userForm->isValid()){

        // on recup le password entre ds le form
        $plainPassword = $userForm->get('password')->getData();
        // on hash le password pour le securisé
        $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('app_login');
       }

       
       return $this->render('front/userform.html.twig', ['userForm' => $userForm->createView()]);
    }

}