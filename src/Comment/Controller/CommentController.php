<?php

namespace App\Comment\Controller;

use App\Article\Constant\ArticleStatus;
use App\Comment\Form\CommentType;
use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/comments', name: 'comments_')]
class CommentController extends AbstractController
{
    #[Route('/create/{article}', name: 'create')]
    #[IsGranted('create', 'comment')]
    #[IsGranted('published', 'article')]
    public function edit(RouterInterface $router, Request $request, EntityManagerInterface $em, Article $article, ?Comment $comment = null): Response
    {
        if (ArticleStatus::PUBLISHED !== $article->getStatus()) {
            throw new AccessDeniedException('Article non publié');
        }

        $comment = new Comment();
        $comment->setArticle($article);
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

    #[Route('/delete/{id}', name: 'delete')]
    #[IsGranted('delete', 'comment')]
    public function delete(EntityManagerInterface $em, Comment $comment): RedirectResponse
    {
        $articleId = $comment->getArticle()->getId();

        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('articles_show', ['id' => $articleId]);
    }
}
