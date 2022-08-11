<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdController extends AbstractController
{

    /**
     * @Route("/ads", name="ads")
     */
    public function index(AdRepository $repo)
    {
        /*
        //instancier un repository
        $repo=$this->getDoctrine()->getRepository(Ad::class);
        $ads =$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads'=>$ads
        ]);
        */
        // l'injection de dépendance
        //$repo=$this->getDoctrine()->getRepository(Ad::class);
        $ads =$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads'=>$ads
        ]);

    }

    /**
     * Permet de créer une annonce
     * @Route("/ads/new",name="ads_create")
     */
    //@IsGranted("ROLE_USER")

    public function create(Request $request,ObjectManager $manager)
    {
        $ad= new Ad();
        $image=new Image();
        $image->setUrl('http://placehold.it/400x200')
                ->setCaption('Titre 1');
        $ad->addImage($image);
        $form=$this->createForm(AdType::class,$ad);
        $form->handleRequest(($request));

        //dump($ad)
        if($form->isSubmitted() && $form->isValid())
        {
            $manager=$this->getDoctrine()->getManager(); 
            foreach($ad->getImages() as $image)
            {
                $image->setAd($ad);
                $manager->persist($image);
            }
            
            $ad->setAuthor($this->getUser());
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                'l\'annonce est envoyé'
            );

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);

        }
        /*
        $form=$this->createFormBuilder($ad)
                    ->add('title')
                    ->add('introduction')
                    ->add('content')
                    ->add('rooms')
                    ->add('price')
                    ->add('coverImage')
                    ->add("save",SubmitType::class,[
                        'label'=>'creer la nouvelle annonce',
                        'attr'=>[
                            'class'=>'btn btn-primary'
                        ]
                    ])
                    ->getForm();
        */

        return $this->render("ad/new.html.twig",[
            "form"=>$form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition/modifcation
     * @Route("/ads/{slug}/edit",name="ads_edit")
     * @return Response
     */

     //@Security("is_granted('ROLE_USER') and user===ad.getAuthor()",message="cette annonce ne vous appartient pas")

     public function edit(Ad $ad,Request $request,ObjectManager $manager)
     {
         $form=$this->createForm(AdType::class,$ad);
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             foreach($ad->getImages() as $image)
             {
                 $image->setAd($ad);
                 $manager->persist($image);
             }
             $manager->persist($ad);
             $manager->persist($image);
         }
         return $this->render('ad/edit.html.twig',[
             'form'=>$form->createView(),
             'ad'=>$ad
         ]);
     }

    /**
     * Montre une annonce
     * @Route("/ads/{slug}/show",name="ad_show")
     * @return response
     */

    public function show(Ad $ad)
    {
        $avgRating = $ad->getAvgRating();
        $owner = $ad->getAuthor();
        return $this->render('ad/show.html.twig',[
            'ad'=>$ad,
            "avgRating" => $avgRating,
            'ad_booking' => $ad->getBookings(),
            'owner' => $owner

        ]);
    }

    /**
     * Suppirme une annonce
     * @Route("/ads/{slug}/delete",name="ads_delete")
     */
// @Security("isGranted('ROLE_USER') and user==ad.getAuthor()",message="Vous ne pouvez pas")
    public function delete(Ad $ad,ObjectManager $manager){

        $manager->remove($ad);
        $manager->flush();
        return $this->redirectToRoute("ads_index");
        $this->addFlash(
            'success',
            "l'annonce {$ad->getTitle()} a été bien supprimée"
        );


    }

    
}
