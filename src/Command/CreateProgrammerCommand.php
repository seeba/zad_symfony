<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\UserFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-programmer',
    description: 'Tworzenie nowego programisty',
)]
class CreateProgrammerCommand extends Command
{
    private $validator;
    private $userFactory;
    private $userRepository;

    public function __construct(
        ValidatorInterface $validator, 
        UserFactory $userFactory,
        UserRepository $userRepository,
    ) {
        $this->validator = $validator;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('firstName', InputArgument::REQUIRED, 'Imię')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Nazwisko')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('pesel', InputArgument::REQUIRED, 'PESEL')
            ->addArgument('password', InputArgument::REQUIRED, 'HAsło')
            ->addArgument('programmingLanguages', InputArgument::IS_ARRAY | InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $data = [
            'first_name' => $input->getArgument('firstName'),
            'last_name' =>$input->getArgument('lastName'),
            'email' => $input->getArgument('email'),
            'pesel' => $input->getArgument('pesel'),
            'password' => $input->getArgument('password'),
        ];

        foreach ($input->getArgument('programmingLanguages') as $programmingLang) {
            $programingLanguage = [
                'name' => $programmingLang
            ];
            $data['programmingLanguages'][] = $programingLanguage;
        }
       
        $user = $this->userFactory->createUser($data, 'CLI');

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $io->error((string) $errors);
            return Command::FAILURE;
        }
        $this->userRepository->add($user, true);

        $io->success('Udało się dodać użytkownika');

        return Command::SUCCESS;
    }
}
