<?php

namespace App\Article\Controller;

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
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    #[IsGranted('show', 'article')]
    public function show(RouterInterface $router, Article $article = null)
    {
        $comment = new Comment();
        $comment->setArticle($article);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $router->generate('comments_create', ['article' => $article->getId()])
        ]);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[Route('/create', name: 'create')]
    #[IsGranted('edit', 'article')]
    public function edit(Request $request, EntityManagerInterface $em, ?Article $article = null): Response
    {
        $isCreate = false;
        if (!$article) {
            $isCreate = true;
            $article = new Article();
            $article->setUser($this->getUser());
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $article->setStatus('DRAFT');
            $article->setUser($this->getUser());

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
