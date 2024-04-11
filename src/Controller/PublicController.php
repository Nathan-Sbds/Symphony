<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Import UserPasswordHasherInterface

class PublicController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;


    public function __construct(ArticleRepository $articleRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->articleRepository = $articleRepository;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        $articles = $this->articleRepository->findAll();
        return $this->render('public/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/{id}', name: 'app_article')]
    public function article($id): Response
    {
        $article = $this->articleRepository->find($id);
        return $this->render('public/article.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/article/{id}/edit', name: 'app_article_edit')]
    public function editArticle($id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            {
                $article = $this->articleRepository->find($id);
                if ($article->getAuthor() !== explode('@', $this->getUser()->getEmail())[0]) {
                    return $this->redirectToRoute('app_accueil');
                }
            }
        }
        $article = $this->articleRepository->find($id);
        return $this->render('public/edit_article.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/article/{id}/delete', name: 'app_article_delete')]
    public function deleteArticle($id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            {
                $article = $this->articleRepository->find($id);
                if ($article->getAuthor() !== explode('@', $this->getUser()->getEmail())[0]) {
                    return $this->redirectToRoute('app_accueil');
                }
            }
        }
        $article = $this->articleRepository->find($id);
        $this->articleRepository->delete($article);
        return $this->redirectToRoute('app_accueil');
    }

    #[Route('/force_add_admin', name: 'force_add_admin')]
    public function forceAddAdmin(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $user->setRoles(['ROLE_ADMIN']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_accueil');
    }

    #[Route('/force_delete_admin', name: 'force_delete_admin')]
    public function forceDeleteAdmin(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_accueil');
    }
}
