<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users',name: "app_users_")]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    )
    {
    }

    #[Route('/', name: 'all',methods: "GET")]
    public function index()
    {
        $users = $this->userRepository->findAll();
        return $this->json([
            'users'=>$users,
            'total'=>count($users)
        ],Response::HTTP_OK,[],[
            'groups'=>'user'
        ]);
    }
    #[Route('/{id}',name:"show",methods:'GET')]
    public function getSingleUser(int $id): Response
    {
        $user = $this->userRepository->findOneBy(['id'=>$id]);
        if(!$user){
            return $this->json([
                'status'=>"failed",
                'message'=>"not found user with this id ".$id
            ],Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'status'=>"success",
            'user'=>$user,
        ],Response::HTTP_OK,[],[
            'groups'=>'user'
        ]);
    }

    #[Route('/',name:"create",methods: 'POST')]
    public function createUser(Request $request):Response
    {
        $jsonData = json_decode($request->getContent(), true);
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        $form->submit($jsonData);
        if($form->isValid()){
            $this->em->persist($user);
            $this->em->flush();
            return $this->json([
                'status'=>"success",
                "user"=>$user
            ],Response::HTTP_CREATED);
        }
        $messageError = $this->getErrorsFromForm($form);
        return $this->json(['status'=>'failed',"message"=>$messageError[0]],Response::HTTP_BAD_REQUEST);
    }
    #[Route('/{id}/edit',name:'update',methods: 'PATCH')]
    public function updateUser(User $user,Request $request):Response
    {
        $jsonData = json_decode($request->getContent(), true);
        if (!$user){
            return $this->json(['status'=>'failed','message'=>'not found user with this id'],Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
       $form->submit($jsonData);
        if($form->isValid()){
            $this->em->persist($user);
            $this->em->flush();
            return $this->json([
                'user'=>$user
            ],Response::HTTP_OK);
        }
        $messageError = $this->getErrorsFromForm($form);
        return $this->json(['status'=>'failed','message'=>$messageError[0]],Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/delete',name:"delete",methods: 'DELETE')]
    public function deleteUser(User $user):Response
    {
        $user->setIsActive(false);
        $this->em->persist($user);
        $this->em->flush();
        return $this->json(null,Response::HTTP_NO_CONTENT);
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
