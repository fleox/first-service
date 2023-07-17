<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;

final class User extends JWTUser
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_CLIENT = 'ROLE_CLIENT';
    const ROLE_CAN_VIEW_ALL = 'ROLE_CAN_VIEW_ALL';

    /** @var string */
    private $email;

    public function __construct(string $username, ?string $email, array $roles = [])
    {
        parent::__construct($username, $roles);
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromPayload($username, array $payload)
    {
        if (array_key_exists('clientId', $payload)) {
            $roles = [self::ROLE_CLIENT];
        } else {
            $roles = [self::ROLE_USER];
        }
        if (isset($payload['resource_access']) && isset($payload['resource_access']['firstservice'])) {
            $roles = array_merge($roles, $payload['resource_access']['firstservice']['roles']);
        }

        return new static($username, array_key_exists('email', $payload) ? $payload['email'] : null, array_unique($roles));
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

}