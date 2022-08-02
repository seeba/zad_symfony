<?php

namespace App\DataFixtures;

use App\Entity\ProgrammingLanguage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User;
        $user->setEmail('poczta@sebastianpluta.pl');
        $user->setFirstName('Sebastian');
        $user->setLastName('Pluta');
        $user->setPesel('77110308432');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'wrz4jk94'));
        $language = new ProgrammingLanguage;
        $language->setName('PHP');
        
        $manager->persist($language);

        $language2 = new ProgrammingLanguage;
        $language2->setName('JAVA');

        $manager->persist($language2);
        $user->addProgrammingLanguage($language);
        $user->addProgrammingLanguage($language2);
        $manager->persist($user);

        
        $manager->flush();
    }
}
