<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;

use App\JsonResponse\FormatData;
use App\Repository\ArticleRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/articles')]
class ArticleController extends AbstractController
{

    public function __construct(private ArticleRepository $articleRepository)
    {
    }

    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $articles = $this->articleRepository->findAll();
        $data = new FormatData($articles);
        return $this->json($data->getFormatData(),Response::HTTP_OK,[],[
            'groups'=>"article"
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,#[CurrentUser] User $user): Response
    {
        $jsonData = json_decode($request->getContent(), true);
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $form->submit($jsonData);
        if ($form->isValid()) {
            $article->setAuthor($user);
            $entityManager->persist($article);
            $entityManager->flush();
            $data = new FormatData([
              [  'id'=>$article->getId(),
                'slug'=>$article->getSlug(),
                'content'=>$article->getContent(),
                'createdAt'=>$article->getCreatedAt()]
            ]);
            return $this->json($data->getFormatData(),Response::HTTP_OK,[],['groups'=>'article']);
        }
        $messageError = $this->getErrorsFromForm($form);
        return $this->json(['status'=>'failed',"message"=>$messageError],Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{slug}', name: 'app_article_show', methods: ['GET'])]
    public function show(string $slug): Response
    {
        $article = $this->articleRepository->findBy(['slug'=>$slug]);
        return $this->json([
            'article'=>$article
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['PUT'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['DELETE'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
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
