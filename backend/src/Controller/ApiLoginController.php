<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/login', name: 'app_api_login')]
    public function index(#[CurrentUser] User $user,JWTTokenManagerInterface $tokenManager): JsonResponse
    {
        dd('login route ');
        if($user===null){
            return $this->json([
                'message'=>"missing credentials",

            ],Response::HTTP_UNAUTHORIZED);
        }
        $token = $tokenManager->create($user);
        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'token' => $token
        ]);
    }
}
