<?php

namespace App\Controller\Admins;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/articles', name: 'admin_articles_')]
class AdminArticleController extends AbstractController
{
    #[Route('/validate/{id}', name: 'validate')]
    public function list(EntityManagerInterface $em, Article $article = null): RedirectResponse
    {
        $article->setStatus('PUBLISHED');
        $em->persist($article);
        $em->flush();

        return $this->redirectToRoute('articles_show', ['id' => $article->getId()]);
    }
}
