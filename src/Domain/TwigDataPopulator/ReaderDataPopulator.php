<?php

declare(strict_types=1);

namespace GimmeBook\Domain\TwigDataPopulator;

use Firebase\JWT\JWT;
use GimmeBook\Domain\Security\JwtKeyProvider;
use GimmeBook\Domain\Security\JwtTokensProducer;
use GimmeBook\Infrastructure\Repository\ReaderRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Common\ById;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

/**
 * Populates the response with reader data if logged in
 */
class ReaderDataPopulator implements TwigDataPopulatorInterface
{
    public function __construct(
        private JwtKeyProvider $jwtKeyProvider,
        private ReaderRepositoryInterface $readerRepository
    ) {
    }

    public function populate(ControllerEvent $event, Environment $twig): void
    {
        $request = $event->getRequest();
        $accessToken = $request->cookies->get('accessToken');
        if (!$accessToken) {
            // user is not logged in
            return;
        }

        try {
            $decoded = JWT::decode($accessToken, $this->jwtKeyProvider->getPublicKey(), [JwtTokensProducer::ALGORITHM]);
        } catch (\Exception $exception) {
            // not valid access token, consider not logged in
            return;
        }

        $userId = $decoded->userId;
        $reader = $this->readerRepository->getOneBySpecification(new ById($userId));
        if (!$reader) {
            // nothing to do
            return;
        }

        $twig->addGlobal(
            'readerData',
            [
                'id' => $reader->getId(),
                'login' => $reader->getLogin(),
                'roleId' => $reader->getRoleId(),
            ]
        );
    }
}
