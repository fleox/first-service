<?php

namespace App\Provider;

use KnpU\OAuth2ClientBundle\Security\User\OAuthUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthKeycloakProvider implements UserProviderInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(private array $roles = ['ROLE_USER'])
    {
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * @param string[] $roles
     */
    public function loadUserByIdentifier(string $identifier, array $roles = []): UserInterface
    {
        $this->roles = !empty($roles) ? array_merge($this->roles, $roles) : $this->roles;

        return new OAuthUser($identifier, $this->roles);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof OAuthUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return OAuthUser::class === $class;
    }
}