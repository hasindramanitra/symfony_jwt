<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SexeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserController extends AbstractController
{

    private $entityManager;

    private $userRepository;

    private $tokenStorageInterface;

    private $jwtManager;
    
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    #[Route('/create_user', name: 'create_user', methods:'POST')]
    public function createUser(Request $request, SexeRepository $sexeRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];
        $username = $data['username'];
        $sexe = $data['sexe'];

        $is_email_exist = $this->userRepository->findByEmail($email);

        if (!$is_email_exist) {
            $new_user = new User();

            $new_user
                ->setEmail($email)
                ->setPassword($password)
                ->setUsername($username)
                ->setSexe($sexeRepository->find($sexe));

            $this->entityManager->persist($new_user);
            $this->entityManager->flush();

            return new JsonResponse([
                'message'=>'User created successfully'
            ]);

        }else{
            return new JsonResponse([
                'message'=>'That email is already used, Please Enter another email.'
            ], 400);
        }
    }

    #[Route('/get_all_users', name:'get_all_users', methods:'GET')]
    public function getAllUsers(TokenInterface $token):Response
    {
        dd($decodedJwtToken = $this->jwtManager->decode($token));
        $users = $this->userRepository->findAll();

        if (!empty($users)) {
            return $this->json($users, 200);
        }else{
            return new JsonResponse([
                'message'=>'No data found into database'
            ], 400);
        }
    }
}
