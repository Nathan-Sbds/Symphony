<?php

namespace App\Command;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:CreateArticles',
    description: 'Create new Article(s)',
)]
class CreateArticlesCommand extends Command
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
            ->addArgument('nb_articles', InputArgument::REQUIRED, 'Nombre d\'articles à créer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $nb_articles = $input->getArgument('nb_articles');
        $io->warning('Création de ' . $nb_articles . ' articles');

        if ($nb_articles<1) return Command::FAILURE;

        for($compteur = 0; $compteur < $nb_articles; $compteur++){
            $io->comment('Création article ' . $compteur);
            $article = new Article();
            $article->setTitle('Article numéro ' . $compteur);
            $article->setDate(new \DateTime());
            $article->setDescription('Description de l\'article numéro ' . $compteur);
            $article->setAuthor('NathanSbds');
            $this->entityManager->persist($article);
        }
        $this->entityManager->flush();
        $io->success('Articles créés avec succès!');

        return Command::SUCCESS;
    }
}
