<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Form\SignupType;
use App\Repository\UserRepository;
use App\Service\PasswordService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auth',name: "auth_")]
class AuthenticationController extends AbstractController
{
    public function __construct(
        private JWTTokenManagerInterface $tokenManager,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private PasswordService $passwordService
    )
    {
    }

    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signUp(Request $request,UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = new User();

        $jsonData = json_decode($request->getContent(), true);
        if ($jsonData === null) {
            return $this->json(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }
        $form = $this->createForm(SignupType::class, $user);
        $form->submit($jsonData);
        if ($form->isValid()) {
            $this->passwordService->hashedPassword($user);
            $this->em->persist($user);
            $this->em->flush();
            $token = $this->tokenManager->create($user);
            return $this->json(['token' => $token,"status"=>"success","id"=>$user->getId()], Response::HTTP_CREATED);
        }

        // Handle form validation errors
        $errors = $this->getErrorsFromForm($form);

        return $this->json(['errors' => $errors[0]], Response::HTTP_BAD_REQUEST);
    }
    #[Route('/login',name:"login",methods:'POST')]
    public function login(Request $request): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        $email = $jsonData['email'];
        $user = $this->userRepository->findOneBy(["email"=>$email]);
        dd($user);
        if(!$user){
            return $this->json(['errors' => "invalid credential"], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(LoginFormType::class,$user);
        $form->handleRequest($request);
        $form->submit($jsonData);
        if($form->isValid()){
            return $this->json(['user'=>$form->getData()]);
        }
        $errors = $this->getErrorsFromForm($form);

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
    private function getErrorsFromForm(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                $childErrors = $this->getErrorsFromForm($childForm);
                if ($childErrors) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
