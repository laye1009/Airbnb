<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder=$encoder;

    }
    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('FR-fr');
        $adminRole=new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser=new User();
        $adminUser->setFirstName('Lior')
                ->setLastName('Chamla')
                ->setEmail('kebe@data.fr')
                ->setHash($this->encoder->encodePassword($adminUser,'password'))
                ->setPicture('https://avatars.io/twitter/liiorC')
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(3)).'</p>')
                ->addUserRole($adminRole);

        $manager->persist($adminUser);
                
        $users=[];

        $genres=['male','female'];

        for ($i=1;$i<=10;$i++){
            $user=new User();
            $genre=$faker->randomElement($genres);
            $picture='https://randomuser.me/api/portraits/';
            $pictureId=$faker->numberBetween(1,99).'.jpg';
            $picture=$picture.($genre=='male'?'men/':'women/').$pictureId;
            $hash=$this->encoder->encodePassword($user,'password');
            $user->setFirstName($faker->firstname($genre))
                ->setLastName($faker->lastname)
                ->setEmail($faker->email)
                ->setPicture($picture)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(3)).'</p>')
                ->setHash($hash);
            $manager->persist($user);
            $users[]=$user;
        }
        $appartIm = array();
        for($i=1;$i <= 11;$i++)
        {
            $appartIm[] = 'appart'.$i.'.jpeg';
        }
        for($i=1;$i<=30;$i++)
        {
            $ad=new Ad();
            $title=$faker->sentence();
            //$slug=$slugify->slugify($title);
            //$coverImage=$faker->imageUrl(1000,350);
            $coverImage=$faker->randomElement($appartIm);
            $introduction=$faker->paragraph(2);
            $content='<p>'.join('<p></p>',$faker->paragraphs(5));
            $user=$users[mt_rand(0,count($users)-1)];

            //$user=$users[mt_rand(0,count($users)-1)];
            $ad->setTitle($title)
            //->setSlug($slug)
            ->setCoverImage($coverImage)
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setPrice(mt_rand(40,200))
            ->setRooms(mt_rand(1,5))
            ->setAuthor($user)
            /*
            ->setAuthor($user)
            */
            ;
            $manager->persist($ad);

            for($j=1;$j<=mt_rand(2,5);$j++)
            {
                $image=new Image();
                $image->setUrl($faker->randomElement($appartIm))
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }
            //Gestion des réservations
            for($j=1; $j<=mt_rand(0,10); $j++){
                $booking=new Booking();
                $createdAt=$faker->dateTimeBetween('-6 months');
                $startDate=$faker->dateTimeBetween('-3 months');
                $duration=mt_rand(3,10);
                $endDate=(clone $startDate)->modify("+$duration days");
                $amount=$ad->getPrice()*$duration;
                $booker=$users[mt_rand(0,count($users)-1)];

                $comment=$faker->paragraph();

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment)
                        ;
                $manager->persist($booking);

                // Gestion des commentaire
                $comment=new Comment();
                if(mt_rand(0,1)){
                    $comment->setContent($faker->paragraph())
                        ->setRating(mt_rand(1,5))
                        ->setAuthor($booker)
                        ->setAd($ad);
                    $manager->persist($comment);

                }
                
            }
            $manager->persist($ad);
            
        }
    
        $manager->flush();
    }
}
