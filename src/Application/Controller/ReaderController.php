<?php

declare(strict_types=1);

namespace GimmeBook\Application\Controller;

use Firebase\JWT\JWT;
use GimmeBook\Application\Service\RedirectToIndexResponseProducer;
use GimmeBook\Domain\Security\JwtKeyProvider;
use GimmeBook\Domain\Security\JwtTokensProducer;
use GimmeBook\Infrastructure\Repository\ReaderRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Common\ById;
use GimmeBook\Infrastructure\Specification\Reader\ByLogin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReaderController
{
    public function __construct(
        private ReaderRepositoryInterface $readerRepository,
        private JwtKeyProvider $jwtKeyProvider,
        private RedirectToIndexResponseProducer $redirectToIndex
    ) {
    }

    public function edit(Request $request, int $readerId): Response
    {
        $newLogin = $request->request->get('newLogin');
        $newPassword = $request->request->get('newPassword');

        if (!$newLogin || !$newPassword) {
            return $this->redirectToIndex->produce('Your data cannot be empty', false);
        }

        $token = $request->cookies->get('accessToken');
        if (!$token) {
            // somebody's trying to hack
            return $this->redirectToIndex->produce('Unable to proceed', false);
        }

        try {
            $decoded = JWT::decode($token, $this->jwtKeyProvider->getPublicKey(), [JwtTokensProducer::ALGORITHM]);
        } catch (\Exception $exception) {
            return $this->redirectToIndex->produce('Something went wrong, please try again', false);
        }

        if ($readerId !== $decoded->userId) {
            // somebody wants to change another one's password
            return $this->redirectToIndex->produce('Unable to proceed', false);
        }

        $reader = $this->readerRepository->getOneBySpecification(new ById($readerId));
        if (!$reader) {
            return $this->redirectToIndex->produce('Something went wrong, please try again', false);
        }

        if ($reader->getLogin() !== $newLogin) {
            // user wants to change his login, check other users
            $sameLoginReader = $this->readerRepository->getOneBySpecification(new ByLogin($newLogin));
            if (!$sameLoginReader) {
                return $this->redirectToIndex->produce('Please, choose another login', false);
            }
        }

        $reader->setLogin($newLogin);
        $reader->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
        $this->readerRepository->save($reader);

        return $this->redirectToIndex->produce('Success!', true);
    }
}
