<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\Commentary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:CreateCommentaries',
    description: 'Create new Commentaries',
)]
class CreateCommentariesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('articleId', InputArgument::REQUIRED, 'Id de l\'Article')
            ->addArgument('nb_commentaries', InputArgument::OPTIONAL, 'Nombre de commentaires à créer', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $articleId = $input->getArgument('articleId');
        $nb_commentaries = $input->getArgument('nb_commentaries');

        $io->warning('Création de commentaires pour l\'article ' . $articleId);

        $article = $this->entityManager->getRepository(Article::class)->FindOneBy(['id' => $articleId]);
        if (!$article) {
            $io->error('Article non trouvé');
            return Command::FAILURE;
        }

        $commentaries = ['Super article', 'Très intéressant', 'Je recommande', 'A lire absolument', 'Je n\'ai pas aimé'];

        for($compteur = 0; $compteur < $nb_commentaries; $compteur++){
            $io->comment('Création commentaire ' . $compteur);
            $newCommentary = new Commentary();
            $newCommentary->setDate(new \DateTime());
            $newCommentary->setContent($commentaries[array_rand($commentaries)]);
            $newCommentary->setAuthor('NathanSbds');
            $newCommentary->setArticle($article);
            $this->entityManager->persist($newCommentary);
        }

        $this->entityManager->flush();
        $io->success('Commentaires créés avec succès!');

        return Command::SUCCESS;
    }
}
