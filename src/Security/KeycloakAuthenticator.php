<?php

namespace App\Security;

use App\Controller\KeycloakController;
use App\Provider\OAuthKeycloakProvider;
use App\Provider\OAuthUserProvider;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class KeycloakAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    public function supports(Request $request): bool
    {
        return KeycloakController::CONNECT_CHECK_ROUTE === $request->attributes->get('_route');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient(), ['redirect_uri' => $this->parameterBag->get('keycloak_redirect_url')]);
    }

    public function getUser(mixed $credentials, UserProviderInterface $userProvider)
    {
        /** @var OAuthKeycloakProvider $oAuthKeycloakProvider */
        $oAuthKeycloakProvider = $userProvider;
        /** @var AccessToken $accessToken */
        $accessToken = $credentials;
        $keycloakUser = $this->getClient()->fetchUserFromToken($accessToken);

        /** @var \stdClass $payload */
        $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $accessToken->getToken())[1]))), null, 512, JSON_THROW_ON_ERROR);
        $roles = $payload->resource_access->page->roles ?? [];

        return $oAuthKeycloakProvider->loadUserByIdentifier(
            array_key_exists('email', $keycloakUser->toArray()) ?
                $keycloakUser->toArray()['email'] : $keycloakUser->getId(), $roles
        );
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('keycloak');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        $targetUrl = $this->router->generate('admin');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->getClient();
        $accessToken = $this->fetchAccessToken($client, ['redirect_uri' => $this->parameterBag->get('keycloak_redirect_url')]);
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {

                $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $accessToken->getToken())[1]))), null, 512, JSON_THROW_ON_ERROR);
                $roles = $payload->resource_access->page->roles ?? [];

                $keycloakUser = $client->fetchUserFromToken($accessToken);
                $oAuthKeycloakProvider = new OAuthKeycloakProvider();

                return $oAuthKeycloakProvider->loadUserByIdentifier(
                    array_key_exists('email', $keycloakUser->toArray()) ?
                        $keycloakUser->toArray()['email'] : $keycloakUser->getId(), $roles
                );
            })
        );
    }
}