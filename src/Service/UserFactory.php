<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\ProgrammingLanguage;
use DateTime;
use Pesel\Pesel;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserFactory 
{
    private $passwordHasher;
    private $mailer;

    public function __construct(UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
    }
    
    public function createUser($data, $fromWhere = 'UI'): User
    {
        $user = new User;

        if (Pesel::isValid($data['pesel'])) {
            $pesel = new Pesel($data['pesel']);
            $user->setBirthDate($pesel->getBirthDate());
        }
    
        $user
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setEmail($data['email'])
            ->setPesel($data['pesel'])
            ->setPassword($this->passwordHasher->hashPassword($user, $data['password']))
            ->setFromWhere($fromWhere);
        foreach ($data['programmingLanguages'] as $programmingLang) {
            $programmingLanguage = new ProgrammingLanguage;
            $programmingLanguage->setName($programmingLang['name']);
            $user->addProgrammingLanguage($programmingLanguage);
        }

        if ($user->getBirthDate()->diff(new DateTime())->y > 17) {
            $user->setIsActive(true);
            $email = (new Email())
                ->from('sebastian@pluta.email')
                ->to($user->getEmail())
                ->subject('hello')
                ->text('hello');

            $this->mailer->send($email);
        }

        return $user;
    }
}