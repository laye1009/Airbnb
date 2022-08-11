<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_booking_index")
     */
    public function index(BookingRepository $repo)
    {
        return $this->render('admin/booking/min_book_index.html.twig', [
            'bookings'=>$repo->findAll()
        ]);
    }

    /**
     * Permet d'éditer une réservation
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     * @return Responce
     */
    public function edit(Booking $booking,Request $request,ObjectManager $manager){
        //$form=$this->createForm(AdminBookingType::class,$booking,['validation_groups'=>["front"]]);
        $form=$this->createForm(AdminBookingType::class,$booking);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation a été bien modifiée"
            );

            return $this->redirectToRoute("admin_booking_index");
        }

        return $this->render('admin/booking/min_book_edit.html.twig',[
            'form'=>$form->createView(),
            'booking'=>$booking
        ]);
    }

    /**
     * Permet de supprimer une réservation 
     * @Route("/admin/bookings/{id}/delete",name="admin_booking_delete")
     * @return Response
     */

     public function delete(Booking $booking,ObjectManager $manager){
         $manager->remove($booking);
         $manager->flush();

         $this->addFlash(
             'success',
             'Reservation modifiée'
         );
         return $this->redirectToRoute("admin_booking_index");
     }
}
