<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Service\StatsService;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dash", name="admin_dashboard")
     */
    //public function index(ObjectManager $manager, UserRepository $repo,StatsService $stats): Response
    public function index(ObjectManager $manager,UserRepository $repo,AdRepository $arepo): Response
    {

        //$users=$manager->createQuery('SELECT count(u) FROM App\Entity\Ad u')->getSingleScalarResult();
        

        
        /*
        $tot=count($repo->findAll());
        echo $tot;
        */
        //$bestAds=$arepo->findBestAds(5);
        
        
        $bestAds=$manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title,a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            ORDER BY note DESC'
        )->setMaxResults(5)->getResult();
        

        

        
        return $this->render('admin/dashboard/dash_index.html.twig',[
            'bestAds'=>$bestAds
        ]

        //,['stats'=>compact('users','bookings','comments')
        //bestAds=$bestAds
        );
    }
}
