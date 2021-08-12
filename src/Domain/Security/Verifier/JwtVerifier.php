<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Security\Verifier;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use GimmeBook\Application\Controller\BookController;
use GimmeBook\Application\Controller\LeaseController;
use GimmeBook\Application\Controller\ReaderController;
use GimmeBook\Domain\Security\JwtKeyProvider;
use GimmeBook\Domain\Security\JwtTokensProducer;
use GimmeBook\Infrastructure\Entity\Security\RefreshToken;
use GimmeBook\Infrastructure\Repository\Security\RefreshTokenRepositoryInterface;
use GimmeBook\Infrastructure\Specification\Security\ByUuid;
use Symfony\Component\HttpFoundation\Request;
use GimmeBook\Application\Controller\MainController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Verifies that the access token or refresh token is valid.
 * Also refreshes the last one.
 * @see \Domain\Security\Verifier\JwtVerifierTest
 */
class JwtVerifier extends BaseVerifier
{
    public const SHOULD_UPDATE_TOKENS_ATTR = 'should-update-tokens';

    protected array $controllersToCheck = [
        MainController::class => ['account'],
        ReaderController::class => [self::WILDCARD],
        LeaseController::class => [self::WILDCARD],
        BookController::class => ['add', 'edit', 'remove'],
    ];

    public function __construct(
        private JwtKeyProvider $jwtKeyProvider,
        private JwtTokensProducer $jwtTokensProducer,
        private RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
    }

    protected function doActionsForAny(Request $request): void
    {
        // check and refresh tokens for any route
        $accessToken = $request->cookies->get('accessToken');

        try {
            if ($accessToken) {
                $decoded = JWT::decode($accessToken, $this->jwtKeyProvider->getPublicKey(), [JwtTokensProducer::ALGORITHM]);

                // the user is defined with valid token, pass its id
                $request->attributes->set('readerId', $decoded->userId);

                // try to refresh because of soon expiration in 60 sec
                if ($decoded->exp < time() + 60) {
                    $this->tryRefreshToken($request);
                }
            } else {
                $this->tryRefreshToken($request);
            }
        } catch (ExpiredException $exception) {
            // try refresh because expired
            $this->tryRefreshToken($request);
            return;
        } catch (\Exception $exception) {
            // token is not valid, nothing to do
        }
    }

    private function tryRefreshToken(Request $request): void
    {
        $refreshToken = $request->cookies->get('refreshToken');
        if (!$refreshToken) {
            return;
        }

        /**
         * @var RefreshToken|null $refreshTokenEntity
         */
        $refreshTokenEntity = $this->refreshTokenRepository->getOneBySpecification(new ByUuid($refreshToken));
        if (!$refreshTokenEntity) {
            return;
        }

        if ($refreshTokenEntity->getCreatedAt()->getTimestamp() + $refreshTokenEntity->getExpiresIn() > time()) {
            // refresh token is not expired, we can refresh it
            $this->setRefreshAttr($refreshTokenEntity->getUserId(), $refreshTokenEntity->getDeviceId(), $request);
        }
    }

    final protected function doCheckMethod(): bool
    {
        return $this->selectedMethods[0] === self::WILDCARD
            || in_array($this->currentMethod, $this->selectedMethods, true);
    }

    final protected function doVerify(Request $request): void
    {
        $accessToken = $request->cookies->get('accessToken');
        if (!$accessToken) {
            throw new UnauthorizedHttpException('Bearer ' . $accessToken);
        }

        try {
            JWT::decode($accessToken, $this->jwtKeyProvider->getPublicKey(), [JwtTokensProducer::ALGORITHM]);
        } catch (ExpiredException $exception) {
            // expiration was handled before
        } catch (\Exception $exception) {
            // not valid access token, throw
            throw new UnauthorizedHttpException('Bearer ' . $accessToken);
        }
    }

    private function setRefreshAttr(int $userId, string $deviceId, Request $request): void
    {
        $request->attributes->set(
            self::SHOULD_UPDATE_TOKENS_ATTR,
            $this->jwtTokensProducer->produce(
                $userId,
                $deviceId,
            )
        );
    }
}
