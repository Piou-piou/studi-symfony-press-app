<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comments', name: 'comments_')]
class CommentController extends AbstractController
{
    #[Route('/create/{article}', name: 'create')]
    public function edit(Request $request, EntityManagerInterface $em, Article $article): RedirectResponse
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $comment */
            $comment = $form->getData();
            $comment->setStatus('DRAFT');

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'L\'article a été ajouté');
        }

        return $this->redirectToRoute('articles_show', ['id' => $article]);
    }
}
