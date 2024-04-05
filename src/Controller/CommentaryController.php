<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Commentary;

class CommentaryController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function ajouterCommentary(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $articleId = $request->request->get('article_id');
        $content = $request->request->get('content');

        $commentary = new Commentary();
        $commentary->setContent($content);
        $article = $this->entityManager->getRepository(Article::class)->FindOneBy(['id' => $articleId]);
        $commentary->setArticle($article);
        $user = $this->getUser();
        $email = $user->getEmail();
        $username = explode('@', $email)[0];
        $commentary->setAuthor($username);
        $commentary->setDate(new \DateTime());


        $this->entityManager->persist($commentary);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_article', ['id' => $articleId]);
    }
}

