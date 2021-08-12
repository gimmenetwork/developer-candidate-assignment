<?php

declare(strict_types=1);

namespace GimmeBook\Application\Controller;

use GimmeBook\Domain\Security\Access\Enum\Role;
use GimmeBook\Domain\Security\JwtTokensProducer;
use GimmeBook\Infrastructure\Entity\Reader;
use GimmeBook\Infrastructure\Repository\ReaderRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Reader\ByLogin;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class AuthController
{
    public function __construct(
        private ReaderRepositoryInterface $readerRepository,
        private JwtTokensProducer $jwtTokensProducer,
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    private function getSignUpPageData(): array
    {
        return [
            'formTitle' => 'Sign Up',
            'anotherFormUrl' => $this->urlGenerator->generate('loginPage'),
            'anotherFormTitle' => 'I already have an account',
            'buttonText' => 'Register',
        ];
    }

    public function signUpPage(): Response
    {
        return new Response(
            $this->twig->render(
                'authForm/authForm.twig',
                $this->getSignUpPageData(),
            )
        );
    }

    private function generateErrorResponse(array $data, string $error, int $statusCode): Response
    {
        $data['error'] = $error;
        return new Response(
            $this->twig->render(
                'authForm/authForm.twig',
                $data,
            ),
            $statusCode
        );
    }

    private function generateErrorSignUpResponse(string $error, int $statusCode): Response
    {
        $data = $this->getSignUpPageData();
        return $this->generateErrorResponse($data, $error, $statusCode);
    }

    public function signUpAction(Request $request): Response
    {
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        $deviceId = $request->request->get('deviceId');

        if (!$login || !$password) {
            return $this->generateErrorSignUpResponse('Please, fill up all fields', Response::HTTP_UNAUTHORIZED);
        }

        if (!$deviceId) {
            return $this->generateErrorSignUpResponse('Please, use valid device', Response::HTTP_BAD_REQUEST);
        }

        // check if login is occupied
        $suchLogin = $this->readerRepository->getOneBySpecification(new ByLogin($login));
        if ($suchLogin) {
            return $this->generateErrorSignUpResponse('Please, choose another login', Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // reader by default, change in db to become and admin
        // just noticed that Reader better would be called User :(
        $reader = new Reader($login, $hashedPassword, Role::READER);
        $reader = $this->readerRepository->save($reader);

        return $this->createSuccessResponse($reader->getId(), $deviceId);
    }

    private function createSuccessResponse(int $readerId, string $deviceId): RedirectResponse
    {
        $response = new RedirectResponse($this->urlGenerator->generate('index'));
        $tokens = $this->jwtTokensProducer->produce($readerId, $deviceId);
        $domain = 'localhost'; // to set cookie, hack!
        $response->headers->setCookie(
            Cookie::create(
                'accessToken',
                $tokens['access_token'],
                time() + JwtTokensProducer::ACCESS_EXPIRATION,
                domain: $domain,
                secure: true,
                sameSite: Cookie::SAMESITE_STRICT
            )
        );
        $response->headers->setCookie(
            Cookie::create(
                'refreshToken',
                $tokens['refresh_token'],
                time() + JwtTokensProducer::REFRESH_EXPIRATION,
                domain: $domain,
                secure: true,
                sameSite: Cookie::SAMESITE_STRICT
            )
        );
        $response->sendHeaders();
        return $response;
    }

    private function getLoginPageData(): array
    {
        return [
            'formTitle' => 'Login',
            'anotherFormUrl' => $this->urlGenerator->generate('signUpPage'),
            'anotherFormTitle' => 'I don\'t have an account',
            'buttonText' => 'Login',
        ];
    }

    private function generateErrorLoginResponse(string $error, int $statusCode): Response
    {
        $data = $this->getLoginPageData();
        return $this->generateErrorResponse($data, $error, $statusCode);
    }

    public function loginPage(): Response
    {
        return new Response(
            $this->twig->render(
                'authForm/authForm.twig',
                $this->getLoginPageData(),
            )
        );
    }

    public function loginAction(Request $request): Response
    {
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        $deviceId = $request->request->get('deviceId');

        if (!$login || !$password) {
            return $this->generateErrorLoginResponse('Please, fill up all fields', Response::HTTP_UNAUTHORIZED);
        }

        if (!$deviceId) {
            return $this->generateErrorLoginResponse('Please, use valid device', Response::HTTP_BAD_REQUEST);
        }

        // check if user exists
        $reader = $this->readerRepository->getOneBySpecification(new ByLogin($login));
        if (!$reader) {
            return $this->generateErrorLoginResponse('Wrong login or password', Response::HTTP_UNAUTHORIZED);
        }

        // verify
        if (!password_verify($password, $reader->getPassword())) {
            return $this->generateErrorLoginResponse('Wrong login or password', Response::HTTP_UNAUTHORIZED);
        }

        return $this->createSuccessResponse($reader->getId(), $deviceId);
    }

    public function logoutAction(): Response
    {
        $response = new RedirectResponse($this->urlGenerator->generate('index'));
        $response->headers->clearCookie('accessToken');
        $response->headers->clearCookie('refreshToken');
        $response->sendHeaders();
        return $response;
    }
}
