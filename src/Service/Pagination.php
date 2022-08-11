<?php
// du cours

namespace App\Service;

use Twig\Environment;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination{
    private $entityClass;
    private $limit=10;
    private $currentPage=1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;
// Envirment $twig : rendre des template; RequestStack au lieu de Request
    public function __construct(ObjectManager $manager,Environment $twig, RequestStack $request,$templatePath){
        /*
        dump($request);
        die();
        */
        $this->route=$request->getCurrentRequest()->attributes->get('_route');
        dump($this->route);
        die();
        $this->manager=$manager;
        $this->twig=$twig;
        $this->templatePath=$templatePath;
    }

    public function setTemplatePath($templatePath){
        $this->templatePath=$templatePath;
        return $this;
    }
    // definition de la valeur de templatePath dans le fichier conf/service.yaml
    public function getTemplatePath(){
        return $this->templatePath;

    }

    public function setRoute($route){
        $this->route=$route;
        return $this;
    }

    public function getRoute(){
        return $this->route;
    }

    public function display(){
        $this->twig->display('admin/partials/pagination.html.twig',[
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]);
    }

    public function getPages(){
        $repo = $this->manager->getRepository($this->entityClass);
        $total=  count($repo->findAll());

        $pages=ceil($total/$this->getLimit());

        return $pages;
    }

    public function getData(){
        // calcul de l'offset
        $offset= $this->currentPage * $this->limit - $this->limit;
        //é2
        $repo=$this->manager->getRepository($this->entityClass);
        $data=$repo->findBy([],[],$this->limit,$offset);
        return $data;
    }

    public function setPage($page){
        $this->currentPage=$page;
        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function setLimit($limit){
        $this->limit=$limit;
        return $this;
    }
    public function getLimit(){
        return $this->limit;
    }
    
    public function setEntityClass($entityClass){
        $this->entityClass=$entityClass;
        return $this;

    }

    public function getEntityClass(){
        return $this->entityClass;
    }
}