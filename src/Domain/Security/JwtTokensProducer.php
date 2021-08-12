<?php

declare(strict_types=1);

namespace GimmeBook\Domain\Security;

use Firebase\JWT\JWT;
use GimmeBook\Infrastructure\Entity\Security\RefreshToken;
use GimmeBook\Infrastructure\Repository\Security\RefreshTokenRepositoryInterface;
use GimmeBook\Infrastructure\Specification\CompoundSpecification;
use GimmeBook\Infrastructure\Specification\Security\ByDeviceId;
use GimmeBook\Infrastructure\Specification\Security\ByUserId;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @see \Domain\Security\JwtTokensProducerTest
 */
class JwtTokensProducer
{
    public const ALGORITHM = 'RS256';
    public const ACCESS_EXPIRATION = 3600; // 1 hr
    public const REFRESH_EXPIRATION = 604800; // 1 week

    private JwtKeyProvider $jwtKeyProvider;
    private RefreshTokenRepositoryInterface $refreshTokenRepository;

    public function __construct(JwtKeyProvider $jwtKeyProvider, RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->jwtKeyProvider = $jwtKeyProvider;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    #[ArrayShape(['access_token' => "string", 'refresh_token' => "string"])]
    public function produce(int $userId, string $deviceId, int $overrideTime = null): array
    {
        $deviceId = substr($deviceId, 0, 20);

        $now = $overrideTime ?? time();
        $payload = [
            'iss' => 'gimmebook',
            'aud' => 'gimmebook-front',
            'iat' => $now,
            'exp' => $now + self::ACCESS_EXPIRATION,
            'userId' => $userId,
            'deviceId' => $deviceId,
        ];
        $jwt = JWT::encode($payload, $this->jwtKeyProvider->getPrivateKey(), self::ALGORITHM);

        // remove prev refresh token
        $existingRefreshTokens = $this->refreshTokenRepository->getBySpecification(
            new CompoundSpecification(
                new ByUserId($userId),
                new ByDeviceId($deviceId),
            )
        );
        if (!empty($existingRefreshTokens)) {
            foreach ($existingRefreshTokens as $existingRefreshToken) {
                $this->refreshTokenRepository->delete($existingRefreshToken);
            }
        }

        // and then create new one
        $refreshToken = new RefreshToken($userId, new \DateTimeImmutable(), self::REFRESH_EXPIRATION, $deviceId);
        $refreshToken = $this->refreshTokenRepository->save($refreshToken);

        return [
            'access_token' => $jwt,
            'refresh_token' => $refreshToken->getUuid(),
        ];
    }
}
