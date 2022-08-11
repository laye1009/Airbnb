<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comment")
     */
    public function index(): Response
    {
        $repo=$this->getDoctrine()->getRepository(Comment::class);
        $comments=$repo->findAll();

        return $this->render('admin/comment/index_min_comment.html.twig', [
            'comments'=>$comments,
        ]);
    }

    /**
     * Permet de modifier un commentaire
     * @Route("/admin/comments/{id}/edit",name="admin_comment_edit")
     * @return
     */
    public function edit(Comment $comment,Request $request,ObjectManager $manager){
        $form=$this->createForm(AdminCommentType::class,$comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "le commentaire numero {$comment->getId()} a été modifié"
            );

        }
        return $this->render('admin/comment/min_edit.html.twig',[

            'comment'=>$comment,
            'form'=>$form->createView( )
        ]);
    }

    /**
     * permet de supprimer un commentaire
     * @Route("/admin/comments/{id}/delete",name="admin_comment_delete")
     * 
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */

     public function delete(Comment $comment,ObjectManager $manager){
         $manager->remove($comment);
         $manager->flush();

         $this->addFlash(
            'success',
            "le commentaire numero {$comment->getId()} a été modifié"
        );
        return $this->redirectToRoute('admin_comment');
     }
}
