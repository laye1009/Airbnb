<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * //@Route("/admin/ads/{page}", name="admin_ads_index",requirements={"page":"\d+"})
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    //@Route("/admin/ads/{page<\d+>?1}": ?: paramètre optionnel; 1: valeur par défaut
    public function index(AdRepository $repo,$page,Pagination $pagination): Response
    {
        $pagination->setEntityClass(Ad::class)
                    ->setPage($page)
                    //->setRoute('admin_booking_index')
                    ;

        $bookings= $pagination->getData();
        /*
        dump($bookings);
        die();
        */
        
        // la Methode find 
        /*
        $ad=$repo->find(332);
        dump($ad);
        $ad=$repo->findOneBy([
            'title'=>332
        ]);
        */
        return $this->render('admin/ad/index_min_ad.html.twig', [
            //'ads'=>$repo->findAll()
            //'pagination'=>$pagination
            'ads'=>$pagination->getData(),
            'pages'=>$pagination->getPages(),
            'page'=>$page
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/edit",name="admin_ads_edit")
     * @param Ad $ad
     * @return Response
     * 

     */

     public function edit(Ad $ad,Request $request,ObjectManager $manager){
         $form=$this->createForm(AdType::class,$ad);
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
             $manager->persist($ad);
             $manager->flush();

             $this->addFlash(
                 'success',
                 "l'annonce  <strong>{$ad->getTitle()} </strong> a été bien enregistrée"
             );
         }
         return $this->render('admin/ad/admin_edit.html.twig',[
             'ad'=>$ad,
             'form'=>$form->createView()
         ]);
     }

     /**
      * Permet de supprimer une annonce
      * @Route("/admin/ads/{id}/delete",name="admin_ads_delete")
      * @param Ad $ad
      * @param ObjectManager $manager
      * @return Response
      */
      public function delete(Ad $ad,ObjectManager $manager){
          if(count($ad->getBookings())>0){
              $this->addFlash(
                  'warning',
                  "Cette annonce est déjà réservée"
              );
          } else {
            $manager->remove($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce {$ad->getTitle()} a été bien supprimée "
            );
          }
          return $this->redirectToRoute('admin_ads_index');

      }
}
