<?php

namespace App\Controller\Admin;

use App\Entity\Licence;
use App\Form\LicenceType;
use App\Repository\LicenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminLicenceController extends AbstractController
{

    /**
     * @Route("admin/licences/", name="admin_list_licence")
     */
    public function listLicence(LicenceRepository $licenceRepository)
    {
        $licences = $licenceRepository->findAll();

        return $this->render('admin/licences.html.twig', ['licences' => $licences]);
    }



    
   /**
     * @Route("admin/licence/{id}", name="admin_show_licence")
     */
    public function showLicence($id, LicenceRepository $licenceRepository)
    {
        $licence = $licenceRepository->find($id);

        return $this->render('admin/licence.html.twig', ['licence' => $licence]);
    }





    /**
     * @Route("admin/add/licence/", name="admin_add_licence")
     */
   public function addLicence(EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface)
   {
       $licence = new Licence();      

       // Création du formulaire
       $licenceForm = $this->createForm(LicenceType::class, $licence); 

       // Utilisation de handleRequest pour demander au formulaire de traiter les infos
       // rentrées dans le formulaire
       // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
       $licenceForm->handleRequest($request);

       if($licenceForm->isSubmitted() && $licenceForm->isValid())
       {



        $mediaFile = $licenceForm->get('media')->getData();

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

                $licence->setMedia($newFilename);
            }





        
        $entityManagerInterface->persist($licence);    // pré-enregistre dans la base de données
        $entityManagerInterface->flush();           // Enregistre dans la pase de données.

        return $this->redirectToRoute('admin_list_licence');
       }

       // redirige vers la page où le formulaire est affiché.
       return $this->render('admin/updatelicence.html.twig', ['licenceForm' => $licenceForm->createView()]);
   }

 





   
   
    /**
     * @Route("admin/update/licence/{id}", name="admin_update_licence")
     */                                       
    public function updateLicence($id, LicenceRepository $licenceRepository, EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface )
    {
       $licence = $licenceRepository->find($id);

       
       $licenceForm = $this->createForm(LicenceType::class, $licence); // a changer

       // Utilisation de handleRequest pour demander au formulaire de traiter les infos
       // rentrées dans le formulaire
       // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
       $licenceForm->handleRequest($request);

       if($licenceForm->isSubmitted() && $licenceForm->isValid())
       {

       

        $mediaFile = $licenceForm->get('media')->getData();

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

            $licence->setMedia($newFilename);
        }



           $entityManagerInterface->persist($licence);
           $entityManagerInterface->flush();

           return $this->redirectToRoute('admin_list_licence');
       }

       // redirige vers la page où le formulaire est affiché.
       return $this->render('admin/updatelicence.html.twig', ['licenceForm' => $licenceForm->createView()]);
    }








    /**
     * @Route("admin/delete/licence/{id}", name="admin_delete_licence")
     */
   public function deleteLicence($id, LicenceRepository $licenceRepository, EntityManagerInterface $entityManagerInterface)
   {
       $licence = $licenceRepository->find($id);
       $entityManagerInterface->remove($licence); // fonction remove supprime lelement sélectionné
       $entityManagerInterface->flush();

       $this->addFlash(
        'notice',
        'Votre licence a été supprimé'
        );
    
       return $this->redirectToRoute("admin_list_licence");
   }






}


