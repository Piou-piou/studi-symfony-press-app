<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/comments', name: 'comments_')]
class CommentController extends AbstractController
{
    #[Route('/create/{article}', name: 'create')]
    public function edit(RouterInterface $router, Request $request, EntityManagerInterface $em, Article $article): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $router->generate('comments_create', ['article' => $article->getId()]),
            'attr' => ['data-turbo-frame' => '_top' ],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setPublishedAt(new \DateTimeImmutable());
            $comment->setUser($this->getUser());

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'L\'article a été ajouté');

            return $this->redirectToRoute('articles_show', ['id' => $article->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form
        ]);
    }
}
