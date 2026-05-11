<?php

namespace App\Command;

use App\Entity\Responsable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer le compte administrateur initial',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $admin = new Responsable();
        $admin->setMatricule('ONCF-ADMIN');
        $admin->setNom('Admin');
        $admin->setPrenom('Système');
        $admin->setEmail('admin@oncf.ma');
        $admin->setFonction('Administrateur système');
        $admin->setDateEmbauche(new \DateTime('2024-01-01'));
        $admin->setService('Direction SI');
        $admin->setDateNomination(new \DateTime('2024-01-01'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));

        $this->em->persist($admin);
        $this->em->flush();

        $io->success('Administrateur créé : admin@oncf.ma / admin123');

        return Command::SUCCESS;
    }
}
