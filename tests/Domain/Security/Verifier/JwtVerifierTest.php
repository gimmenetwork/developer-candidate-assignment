<?php

declare(strict_types=1);

namespace Domain\Security\Verifier;

use GimmeBook\Application\Controller\BookController;
use GimmeBook\Domain\Security\JwtKeyProvider;
use GimmeBook\Domain\Security\JwtTokensProducer;
use GimmeBook\Domain\Security\Verifier\JwtVerifier;
use GimmeBook\Infrastructure\Entity\Security\RefreshToken;
use GimmeBook\Infrastructure\Repository\Security\RefreshTokenRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @covers \GimmeBook\Domain\Security\Verifier\JwtVerifier
 * @codeCoverageIgnore
 */
class JwtVerifierTest extends MockeryTestCase
{
    private JwtVerifier $jwtVerifier;
    private JwtTokensProducer $jwtTokensProducer;

    protected function setUp(): void
    {
        $jwtKetProvider = new JwtKeyProvider();

        $refreshTokenMock = Mockery::mock(RefreshToken::class);
        $refreshTokenMock->allows('getUuid')->andReturn('123123');
        $refreshTokenMock->expects('getUserId')->andReturn(1);
        $refreshTokenMock->expects('getDeviceId')->andReturn('123123');
        $refreshTokenMock->expects('getCreatedAt')->andReturn(new \DateTimeImmutable());
        $refreshTokenMock->expects('getExpiresIn')->andReturn(PHP_INT_MAX);

        $refreshTokenRepository = Mockery::mock(RefreshTokenRepositoryInterface::class);
        $refreshTokenRepository->allows('getBySpecification')->andReturn([]);
        $refreshTokenRepository->expects('getOneBySpecification')->andReturn($refreshTokenMock);
        $refreshTokenRepository->allows('save')->andReturn($refreshTokenMock);

        $this->jwtTokensProducer = new JwtTokensProducer($jwtKetProvider, $refreshTokenRepository);

        $this->jwtVerifier = new JwtVerifier(
            $jwtKetProvider,
            $this->jwtTokensProducer,
            $refreshTokenRepository,
        );
    }

    public function testTokensUpdatedIfAccessExpired(): void
    {
        $testTokens = $this->jwtTokensProducer->produce(1, 'asd', time() - JwtTokensProducer::ACCESS_EXPIRATION - 10);
        $kernel = Mockery::mock(HttpKernelInterface::class);
        $request = new Request(
            attributes: [
                '_controller' => BookController::class . '::add',
            ],
            cookies: [
                'accessToken' => $testTokens['access_token'],
                'refreshToken' => $testTokens['refresh_token'],
            ],
        );
        $this->jwtVerifier->verify(new RequestEvent($kernel, $request, null));

        self::assertIsArray($request->attributes->get(JwtVerifier::SHOULD_UPDATE_TOKENS_ATTR));
    }
}
