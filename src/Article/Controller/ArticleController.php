<?php

namespace App\Article\Controller;

use App\Article\Constant\ArticleStatus;
use App\Article\Form\ArticleType;
use App\Article\Repository\ArticleRepository;
use App\Comment\Form\CommentType;
use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/articles', name: 'articles_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/list.html.twig', [
            'articles' => $articleRepository->findByUser($this->getUser()),
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    #[IsGranted('show', 'article')]
    public function show(RouterInterface $router, Article $article = null)
    {
        $form = $this->createForm(CommentType::class, (new Comment())->setArticle($article), [
            'action' => $router->generate('comments_create', ['article' => $article->getId()])
        ]);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form,
            'article_draft' => ArticleStatus::DRAFT,
            'article_published' => ArticleStatus::PUBLISHED
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[Route('/create', name: 'create')]
    #[IsGranted('edit', 'article')]
    public function edit(Request $request, EntityManagerInterface $em, ?Article $article = null): Response
    {
        $isCreate = !$article;
        $article = $article ?? new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', $isCreate ? 'L\'article a été créé' : 'L\'article a été modifié');

            return $this->redirectToRoute('articles_list');
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form,
            'is_create' => $isCreate,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(EntityManagerInterface $em, Article $article): RedirectResponse
    {
        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'L\'article a été supprimé');

        return $this->redirectToRoute('articles_list');
    }
}
