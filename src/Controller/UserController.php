<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Pesel\Pesel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user')]
class UserController extends AbstractController
{
   
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        UserRepository $userRepository, 
        UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pesel = new Pesel($user->getPesel());
            $user->setBirthDate($pesel->getBirthDate());
            $user->setFromWhere('UI');
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{days}', name: 'app_user_index', methods: ['GET'], defaults: ['days' => 30])]
    public function index($days, UserRepository $userRepository): Response
    {   
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findCreatedInRecentDays($days),
            'days' => $days
        ]);
    }
}
