<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:adult-activate',
    description: 'Aktywowanie doroslych uzytkowników i wysłanie email',
)]
class ActivateAdultCommand extends Command
{
    private $userRepository;
    private $mailer;

    public function __construct(UserRepository $userRepository, MailerInterface $mailer)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }
    
    protected function configure(): void
    {
       
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->userRepository->findNoActive();

        foreach ($users as $user) {
            $user->setIsActive(true);
            $email = (new Email())
                ->from('sebastian@pluta.email')
                ->to($user->getEmail())
                ->subject('hello')
                ->text('hello');
            $this->mailer->send($email);
        }
    
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
