<?php

namespace App\Controller\Api;

use App\Dto\Response\ApiResponse;
use App\Service\AuthService;
use App\Validators\LoginValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api") */
class AuthController extends AbstractController
{
    public function __construct(
        private AuthService $authService
    )
    {
    }

    #[Route('/login', name: 'api-login', methods: 'POST')]
    public function login(Request $request, LoginValidator $validator): Response
    {
        try {
            $validator->validate($request->toArray());
            if ($request->toArray()['username'] == 'admin' && $request->toArray()['password'] == 'password') {
                $this->authService->login();
            } else {
                return (new JsonResponse(["error" => "Invalid Credentials"], 403));
            }

            return (new ApiResponse("", Response::HTTP_OK))->send();

        } catch (\Exception $e) {
            return (new ApiResponse($e->getMessage(), Response::HTTP_BAD_REQUEST))->send();
        }
    }

    #[Route('/logout', name: 'api-logout')]
    public function logout(): Response
    {
        $this->authService->logout();
        return (new ApiResponse("", Response::HTTP_OK))->send();
    }
}
