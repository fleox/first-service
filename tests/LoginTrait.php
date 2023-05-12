<?php

namespace App\Tests;

use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait LoginTrait
{
    /** @var KernelBrowser */
    private KernelBrowser $client;

    private JWTEncoderInterface $jwtEncoder;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $encoder = $this->client->getContainer()->get('lexik_jwt_authentication.encoder');
        if (!($encoder instanceof JWTEncoderInterface)) {
            throw new Exception('bad encoder');
        }
        $this->jwtEncoder = $encoder;
    }

    protected function setClient(KernelBrowser $client): void
    {
        $this->client = $client;
    }

    /**
     * Crée un utilisateur fictif et retourne son token.
     *
     * @param string $role role de l'utilisateur ('ROLE_ADMIN', 'ROLE_USER')
     * @throws JWTEncodeFailureException
     */
    private function createUser(string $role, string $uid = 'XXXXXX'): string
    {
        $data = [
            'sub' => $uid,
            'email' => 'test.test@test.test',
            'resource_access' => [
                'profile' => [
                    'roles' => [
                        $role,
                    ],
                ],
            ],
        ];

        if ('User::ROLE_CLIENT' == $role) {
            $data['clientId'] = 'service-connect';
        }

        if (array_key_exists('clientId', $data) && 'User::ROLE_USER' == $role) {
            unset($data['clientId']);
        }

        if ('IS_OWN_THREAD' == $role) {
            $data['resource_access']['profile'] = [
                'role' => [$role],
            ];
        }

        return $this->jwtEncoder->encode($data);
    }
}
