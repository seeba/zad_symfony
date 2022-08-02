<?php

namespace App\Controller\API;

use App\Repository\UserRepository;
use App\Service\UserFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{  
    #[Route('api/user', name: 'app_api_user_create', methods: ['POST'])]
    public function create(
        Request $request, 
        ValidatorInterface $validator,
        UserFactory $userFactory,
        UserRepository $userRepository,
    ) {
        $data = json_decode($request->getContent(), true);

        $user = $userFactory->createUser($data, 'API');
        
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST);
        }
        
        $userRepository->add($user, true);

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
