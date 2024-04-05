<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Article;

class ArticleController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function ajouterArticle(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $title = $request->request->get('title');
        $content = $request->request->get('content');

        $article = new Article();
        $article->setTitle($title);
        $article->setDescription($content);
        $user = $this->getUser();
        $email = $user->getEmail();
        $username = explode('@', $email)[0];
        $article->setAuthor($username);
        $article->setDate(new \DateTime());


        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // Rediriger vers la page d'accueil ou une autre page
        return $this->redirectToRoute('app_accueil');
    }

    public function editerArticle(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $articleId = $request->request->get('article_id');
        $title = $request->request->get('title');
        $content = $request->request->get('content');

        $article = $this->entityManager->getRepository(Article::class)->FindOneBy(['id' => $articleId]);
        $article->setTitle($title);
        $article->setDescription($content);
        $article->setDate(new \DateTime());

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_article', ['id' => $articleId]);
    }
}

