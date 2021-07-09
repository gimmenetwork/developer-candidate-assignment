<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private BookService $bookService,
        private AuthService $authService,
    )
    {
    }

    #[Route('/', name: 'homepage')]
    public function index(Request $request): Response
    {
        $genreList = $this->bookService->getGenreList();

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, ["required"=>false])
            ->add('author', TextType::class, ["required"=>false])
            ->add('genre', ChoiceType::class, [
                'choices' => array_merge(["Select Genre" => ""], $genreList),
                'required' => false
            ])
            ->add('filter', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $books = $this->bookService->filterBooks($form->getData());
        } else {
            $books = $this->bookService->getAllBooks();
        }

        return $this->render('homepage.html.twig', array(
            'form' => $form->createView(),
            'books' => $books
        ));
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('login', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()['username'] == 'admin' && $form->getData()['password'] == 'password') {
                $this->authService->login();
                return $this->redirectToRoute('homepage');
            } else {
                $this->addFlash('error', 'Wrong credentials');
            }
        }

        return $this->render('login.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        $this->authService->logout();
        return $this->redirectToRoute('homepage');
    }
}
