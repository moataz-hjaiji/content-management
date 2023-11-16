<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Auth\LoginFormType;
use App\Form\Auth\ResetPasswordType;
use App\Form\Auth\SignupType;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\Email\VerificationEmail;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/auth',name: "auth_")]
class AuthenticationController extends AbstractController
{
    public function __construct(
        private JWTTokenManagerInterface        $tokenManager,
        private  EntityManagerInterface $em,
        private AuthService                     $authService,
        private UserRepository $userRepository
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signUp(Request $request,VerificationEmail $verificationEmail): JsonResponse
    {
        $user = new User();
        $jsonData = json_decode($request->getContent(), true);
        if ($jsonData === null) {
            return $this->json(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(SignupType::class, $user);
        $form->submit($jsonData);
        if ($form->isValid()) {
            $verificationEmail->sendEmail($user->getEmail(),"email sent to ".$user->getEmail());
            $this->authService->hashedPassword($user);
            $this->em->persist($user);
            $this->em->flush();
            $token = $this->tokenManager->create($user);
            return $this->json(['token' => $token,"status"=>"success","id"=>$user->getId()], Response::HTTP_CREATED);
        }

        // Handle form validation errors
        $errors = $this->getErrorsFromForm($form);

        return $this->json(['errors' => $errors[0]], Response::HTTP_BAD_REQUEST);
    }
    #[Route('/login', name:"login",methods: ['POST'])]
    public function login(Request $request,#[CurrentUser] User $user,Security $security): JsonResponse
    {
        $jsonData = json_decode($request->getContent(), true);
        if(!$user){
            return $this->json(['errors' => "no user find with this email "],Response::HTTP_BAD_REQUEST);
        }
        $isValidPassword = $this->authService->isValidPassword($user,$jsonData['password']);
        if(!$isValidPassword){
            return $this->json(['errors' => "invalid credential"], Response::HTTP_BAD_REQUEST);
        }
        $form = $this->createForm(LoginFormType::class,$user);
        $form->handleRequest($request);
        $form->submit($jsonData);
        if($form->isValid()){
            $token = $this->tokenManager->create($user);
            $this->authService->authenticate($request);
            $security->login($user);
            return $this->json(['token' => $token,"status"=>"success","id"=>$user->getId()], Response::HTTP_OK);
        }
        $errors = $this->getErrorsFromForm($form);
        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/me',name:"me",methods: ['GET','POST'])]
    public function getAuthenticatedUser(#[CurrentUser] User $user,TokenInterface $token): JsonResponse
    {

        if (!$user) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $userData = [
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            // Add other properties as needed
        ];

        return $this->json($userData,Response::HTTP_OK);
    }
    #[Route('/logout',name:"logout",methods: 'GET')]
    public function logout()
    {

    }
    private function getErrorsFromForm(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true ) as $error) {
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

    #[Route('/verifEmail/{token}',name:"verif_email",methods: 'POST')]
    public function verifEmail( $token,JWSProviderInterface $JWSProvider):Response
    {
        try {
            $jws = $JWSProvider->load($token);
            if($jws->isExpired()){
                return $this->json(['status'=>'failed',"message"=>"expired token"],Response::HTTP_BAD_REQUEST);
            }
            $payload = $jws->getPayload();
            $user = $this->userRepository->findOneBy(['email'=>$payload['username']]);
            $user->setIsActive(true);
            $this->em->persist($user);
            $this->em->flush();
            return $this->json(['status'=>"success","message"=>"account active successfully"],Response::HTTP_OK);
        } catch (Exception $e){
            return $this->json(['status'=>"failed","message"=>"invalid Token","error"=>$e->getMessage()],Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/resetPassword',name:"reset_password",methods: 'POST')]
    public function resetPassword(Request $request,#[CurrentUser] User $user):Response
    {
        $jsonData = json_decode($request->getContent(),true);
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        $form->submit($jsonData);
        if($form->isValid()){
            $oldPasswordIsValid = $this->authService->isValidPassword($user,$jsonData['oldPassword']);
            if(!$oldPasswordIsValid){
                return $this->json(['status'=>'failed',"message"=>"old password is invalid"],Response::HTTP_BAD_REQUEST);
            }
            $user->setPassword($jsonData['newPassword']);
             $user = $this->authService->hashedPassword($user);
            $this->em->persist($user);
            $this->em->flush();
            return $this->json(['status'=>'success',"message"=>"user updated successfully"],Response::HTTP_OK);
        }
        $messageError = $this->getErrorsFromForm($form);
        return $this->json(['status'=>'failed',"message"=>$messageError],Response::HTTP_BAD_REQUEST);
    }

}
