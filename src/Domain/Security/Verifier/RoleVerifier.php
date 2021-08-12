<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Security\Verifier;

use Firebase\JWT\JWT;
use GimmeBook\Application\Controller\BookController;
use GimmeBook\Application\Controller\LeaseController;
use GimmeBook\Application\Controller\ReaderController;
use GimmeBook\Domain\Security\Access\Enum\Role;
use GimmeBook\Domain\Security\JwtKeyProvider;
use GimmeBook\Domain\Security\JwtTokensProducer;
use GimmeBook\Infrastructure\Repository\ReaderRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Common\ById;
use Symfony\Component\HttpFoundation\Request;
use GimmeBook\Application\Controller\MainController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Verifies that a user has access by its role
 */
class RoleVerifier extends BaseVerifier
{
    protected array $controllersToCheck = [
        MainController::class => [
            'account' => Role::READER,
        ],
        ReaderController::class => [
            self::WILDCARD => Role::READER,
        ],
        LeaseController::class => [
            self::WILDCARD => Role::READER,
        ],
        BookController::class => [
            'add' => Role::ADMIN,
            'edit' => Role::ADMIN,
            'remove' => Role::ADMIN,
        ],
    ];

    public function __construct(
        private JwtKeyProvider $jwtKeyProvider,
        private ReaderRepositoryInterface $readerRepository
    ) {
    }

    final protected function doCheckMethod(): bool
    {
        return isset($this->selectedMethods[self::WILDCARD]) || isset($this->selectedMethods[$this->currentMethod]);
    }

    final protected function doVerify(Request $request): void
    {
        $accessToken = $request->cookies->get('accessToken');

        try {
            $decoded = JWT::decode($accessToken, $this->jwtKeyProvider->getPublicKey(), [JwtTokensProducer::ALGORITHM]);
        } catch (\Exception $exception) {
            // not valid access token, throw
            throw new UnauthorizedHttpException('Bearer ' . $accessToken);
        }

        $userId = $decoded->userId;
        $reader = $this->readerRepository->getOneBySpecification(new ById($userId));

        if (!$reader) {
            // not valid reader, throw
            throw new UnauthorizedHttpException('Bearer ' . $accessToken);
        }

        $requiredRole = $this->selectedMethods[$this->currentMethod] ?? $this->selectedMethods[self::WILDCARD];
        // the higher role the better
        if ($reader->getRoleId() < $requiredRole) {
            // user has no access
            throw new UnauthorizedHttpException('Bearer ' . $accessToken);
        }
    }
}
