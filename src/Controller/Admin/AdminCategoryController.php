<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCategoryController extends AbstractController
{

    /**
     * @Route("admin/categories/", name="admin_list_category")
     */
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('admin/categories.html.twig', ['categories' => $categories]);
    }



    
   /**
     * @Route("admin/category/{id}", name="admin_show_category")
     */
    public function showCategory($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        return $this->render('admin/category.html.twig', ['category' => $category]);
    }





    /**
     * @Route("admin/add/category/", name="admin_add_category")
     */
   public function addCategory(EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface)
   {
       $category = new Category();      

       // Création du formulaire
       $categoryForm = $this->createForm(CategoryType::class, $category); 

       // Utilisation de handleRequest pour demander au formulaire de traiter les infos
       // rentrées dans le formulaire
       // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
       $categoryForm->handleRequest($request);

       if($categoryForm->isSubmitted() && $categoryForm->isValid())
       {


        $mediaFile = $categoryForm->get('media')->getData();
 
        if($mediaFile)
        {
            // on cree un nom unique avec le nom original de l image pour eviter tout pb
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            // on utilise slugg sur le nom original d elimage pour avoir un nom valide
            $safeFileName = $sluggerInterface->slug($originalFilename);
            // on ajoute un id unique au nom de limage
            $newFilename = $safeFileName . '-'  . uniqid() . '.' . $mediaFile->guessExtension();
            
            // on deplace le fichier dans le dossier public/media
            //la destination du fichier est enregistre dans image_directory
            //qui est defini dans le fichier  config\services.yaml
            $mediaFile->move($this->getParameter('images_directory'), $newFilename);

            $category->setMedia($newFilename);
        }




           $entityManagerInterface->persist($category);    // pré-enregistre dans la base de données
           $entityManagerInterface->flush();           // Enregistre dans la pase de données.media category

           return $this->redirectToRoute('admin_list_category');
       }

       // redirige vers la page où le formulaire est affiché.
       return $this->render('admin/updatecategory.html.twig', ['categoryForm' => $categoryForm->createView()]);
   }






   
   
    /**
     * @Route("admin/update/category/{id}", name="admin_update_category")
     */                                       
    public function updateCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface, Request $request )
    {
       $category = $categoryRepository->find($id);

       
       $categoryForm = $this->createForm(CategoryType::class, $category); // a changer

       // Utilisation de handleRequest pour demander au formulaire de traiter les infos
       // rentrées dans le formulaire
       // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
       $categoryForm->handleRequest($request);

       if($categoryForm->isSubmitted() && $categoryForm->isValid()){
           $entityManagerInterface->persist($category);
           $entityManagerInterface->flush();

           return $this->redirectToRoute('admin_list_category');
       }

       // redirige vers la page où le formulaire est affiché.
       return $this->render('admin/updatecategory.html.twig', ['categoryForm' => $categoryForm->createView()]);
    }








    /**
     * @Route("admin/delete/category/{id}", name="admin_delete_category")
     */
   public function deleteCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface)
   {
       $category = $categoryRepository->find($id);
       $entityManagerInterface->remove($category); // fonction remove supprime le product sélectionné
       $entityManagerInterface->flush();

       $this->addFlash(
        'notice',
        'Votre categorie a été supprimé'
        );
    
       return $this->redirectToRoute("admin_list_category");
   }






}


