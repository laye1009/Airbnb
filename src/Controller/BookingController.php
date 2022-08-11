<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use App\Repository\BookingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/booking/edit", name="edit_booking")
     */
    public function edit_book(Ad $ad,Request $request,ObjectManager $manager,Booking $booking): Response
    {
        //$booking=new Booking();
        //var_dump($request->query);die();
        $form=$this->createForm(BookingType::class,$booking);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user=$this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);
            // Si les dates ne sont pas dispo, message d'erreur
            if(!$booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    'Vos dates sont réservées'
                );
            } else{
                $manager->persist($booking);
                $manager->flush();
    
                return $this->redirectToRoute('booking_show',['id'=>$booking->getId(),
                'withAlert'=>true]);
            }
            /*
            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('booking_show',['id'=>$booking->getId(),
            'withAlert'=>true]); */
        }
        
        return $this->render('booking/index_book.html.twig', [
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }
        /**
     * @Route("/ads/{slug}/book/create", name="create_booking")
     */
    public function create_book(Ad $ad,Request $request,ObjectManager $manager,BookingRepository $repo): Response
    {
        //$booking=new Booking();
        //var_dump($request->query);die();
        //var_dump(//$repo->findUserBookings($ad->getId()));die();
        //var_dump($ad->getBookings());die();
        //var_dump($ad->getNotAvailableDays());die();
        $ad_booking = $repo->findUserBookings($ad->getId());
        if(count(array_map('current',$repo->findUserBookings($ad->getId()))) > 0)
        {
            $notAvailableDays = $ad->getNotAvailableDays();
            $notAvailableFormatted = [];
            foreach($notAvailableDays as $day)
                {
                    $notAvailableFormatted[] = $day->format('d-m-Y');
                }
        }
        //var_dump($notAvailableFormatted);die();
        if(!$this->getUser())
        {
            $this->addFlash(
                'warning',
                'Veuillez vous connecter, SVP.'
            );
            return $this->redirectToRoute("ad_show",['slug'=>$ad->getSlug()]);
        }

        $booking = new Booking();
        $form=$this->createForm(BookingType::class,$booking);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user=$this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);
            // Si les dates ne sont pas dispo, message d'erreur
            if(!$booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    'Vos dates sont réservées'
                );
            } else{
                $manager->persist($booking);
                $manager->flush();
    
                return $this->redirectToRoute('booking_show',['id'=>$booking->getId(),
                'withAlert'=>true]);
            }
            /*
            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('booking_show',['id'=>$booking->getId(),
            'withAlert'=>true]); */
        }
        
        return $this->render('booking/index_book.html.twig', [
            'ad'=>$ad,
            'notAvailable' => implode('|',$notAvailableFormatted),
            "ad_booking" => $ad_booking,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     * @Route("/booking/{id}",name="booking_show")
     * 
     * @param Booking $booking
     * @return Response
     */
    
    public function book_show(Booking $booking,Request $request,ObjectManager $manager){
        $comment=new Comment();
        $form=$this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());
            $manager->persist($comment);
        $manager->flush();
        }
        return $this->render('booking/book_show.html.twig',[
            'booking'=>$booking,
            'form'=>$form->createView()
        ]);
    }
}
