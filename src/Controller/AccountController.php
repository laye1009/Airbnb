<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\AdRepository;
use App\Repository\BookingRepository;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     
     */
    public function login(AuthenticationUtils $utils,Request $request): Response
    {
        $error=$utils->getLastAuthenticationError();
        $username=$utils->getLastUsername();
        //var_dump($request->query->get('email'));die();
        //var_dump(empty($request->query));die();

        return $this->render('account/login.html.twig',[
            'hasError'=>$error!==null,
            'username'=>$username
        ]);
    }


    /**
     * @Route("/logout",name="account_logout")
     * @return void
     */

    public function logout(){
        // rien
        $this->addFlash(
            'success',
            'Vous êtes bien déconnecté'
        );
    }

    /**
    * @Route("/register",name="account_register")
    */

     public function register(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder)
     {
         $user=new User();
         $form=$this->createForm(RegistrationType::class,$user);
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid())
         {
             $hash=$encoder->encodePassword($user,$user->getHash());
             $user->setHash($hash);
             $manager->persist($user);
             $manager->flush();

             $this->addFlash(
                 'success',
                 'Votre compte a été bien créé'
             );
             return $this->redirectToRoute('account_login');
         }

         return $this->render('account/registration.html.twig',
         ['form'=>$form->createView()]);

     }
     

     /**
      * modification de profile
      * @Route("/account/profile",name="account_profile")
      * 
      */
//@IsGranted("ROLE_USER")
      public function profile(Request $request,ObjectManager $manager)
      {
          $user=$this->getUser();
          $form=$this->createForm(AccountType::class,$user);
          $form->handleRequest($request);
          if($form->isSubmitted() && $form->isValid())
          {
              $manager->persist($user);
              $manager->flush();
              $this->addFlash(
                  'success',
                  "les données su profil ont été enregistrées"
              );
          }
          return $this->render('account/profile.html.twig',[
              'form'=>$form->createView()
          ]);
      }

      /**
       * @Route("/update-pwd",name="account_pwd")
       * @return response
       */
//@IsGranted("ROLE_USER")
       public function updatePwd(Request $request,UserPasswordEncoderInterface $encoder,ObjectManager $manager)
       {
           $pwdUp=new PasswordUpdate();// l'entité créée
           $user=$this->getUser();
           $form=$this->createForm(PasswordUpdateType::class,$pwdUp);
           $form->handleRequest($request);
           if($form->isSubmitted() && $form->isValid())
           {
               if(!password_verify($pwdUp->getOldPassword(),$user->getHash())){
                   $form->get("oldPassword")->addError(new FormError("Mot de passe actuel incorrect"));

               } else 
               {
                   $newPassword=$pwdUp->getNewPassword();
                   $hash=$encoder->encodePassword($user,$newPassword);
                   $user->setHash($hash);
                   $manager->persist($user);
                   $manager->flush();
                   $this->addFlash(
                       'success',
                       "votre mot de pas a été bien modiifé"
                   );
                   return $this->redirectToRoute("account_login");
               }


           }
           return $this->render('account/password.html.twig',[
               'form'=>$form->createView()
           ]);
       }

       /**
        * Permet d'afficher le profild de l'utilisateur connecté
        * @Route("/account",name="account_index")
        * paramConverter("User")
        * @return response
        */
        public function myAccount(){
            //$em=$this->getDoctrine()->getManager();
            //$user=$urepo->findOneBy(['firstName']);
            return $this->render('user/user_index.html.twig',[
                'user'=>$this->getUser()
            ]);
        }

        /**
         * Permet d'afficher la liste des réservations faite par un utilisateur
         * 
         * @Route("/account/bookings",name="account_bookings")
         * @return response
         */
         public function bookings(AdRepository $ad_repo, BookingRepository $repo){
            $user = $this->getUser();
            //var_dump($user->getBookings()['bookings']);die();
            $bookings = $repo->findByBooker(['booker_id'=>$user->getId()]);
            $ads = [];
            foreach ($bookings as $book)
            {
                $ads[] = $ad_repo->findById(['id' => $book->getAd()->getId()])[0];
            }
             return $this->render('booking/book_list.html.twig',[
                'bookings' => $bookings,
                 'ads' => $ads
             ]);
         }
}
