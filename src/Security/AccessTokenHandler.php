<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{

    public function __construct(#[Autowire('%apiKey%')] private string $apiKey)
    {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        if ($accessToken === $this->apiKey) {

            return new UserBadge('apiKey');
        }

        throw new BadCredentialsException('Invalid credentials.');
    }
}
