<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Import UserPasswordHasherInterface

//1 ArticleRepository a ajouter en auto-wiring
//1.5 Créer une route pour la méthode index
//2 On charge les articles
//3 On retourne la vue twig
//4 On modifie la vue twig pour avoir les articles visibles

//5on créé les autres vue articles (affiche un article et ses commentaires)
//6On charge les articles et commentaires
//7On passe les infos a la vue twig
//8On modifie la vue twig

//9On créé un lien dans la vue twig accueil pour aller vers la route article

class PublicController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;


    // Inject UserPasswordHasherInterface via constructor
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

    #[Route('/force_admin', name: 'force_admin')]
    public function forceAdmin(): Response
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
}
