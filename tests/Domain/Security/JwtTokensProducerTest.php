<?php

namespace Domain\Security;

use GimmeBook\Domain\Security\JwtKeyProvider;
use GimmeBook\Domain\Security\JwtTokensProducer;
use GimmeBook\Infrastructure\Entity\Security\RefreshToken;
use GimmeBook\Infrastructure\Repository\Security\RefreshTokenRepositoryInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \GimmeBook\Domain\Security\JwtTokensProducer
 * @codeCoverageIgnore
 */
class JwtTokensProducerTest extends MockeryTestCase
{
    private JwtTokensProducer $jwtTokensProducer;

    protected function setUp(): void
    {
        $refreshToken = Mockery::mock(RefreshToken::class);
        $refreshToken->expects('getUuid')->andReturn('asdasd');

        $refreshTokenRepository = Mockery::spy(RefreshTokenRepositoryInterface::class);
        $refreshTokenRepository->expects('getBySpecification')->andReturn([$refreshToken]);
        $refreshTokenRepository->expects('save')->andReturn($refreshToken);
        $refreshTokenRepository->expects('delete')->once();

        $this->jwtTokensProducer = new JwtTokensProducer(new JwtKeyProvider(), $refreshTokenRepository);
    }

    public function testExistingTokensDeletedBeforeNew(): void
    {
        $this->jwtTokensProducer->produce(1, 'qwerty');
    }
}
