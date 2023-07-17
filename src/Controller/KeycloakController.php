<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KeycloakController extends AbstractController
{
    final public const CONNECT_CHECK_ROUTE = 'connect_check';

    #[Route('/connect', name: 'connect_start')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        // will redirect to Keycloak!
        return $clientRegistry
            ->getClient('keycloak') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([], ['redirect_uri' => $this->getParameter('keycloak_redirect_url')]);
    }

    #[Route('/connect/check', name: self::CONNECT_CHECK_ROUTE)]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): Response
    {  // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)
        dump("qsdsqd");die;
        return new RedirectResponse($this->generateUrl('admin'));
    }

    #[Route('/logout', name: 'logout')]
    public function logoutAction(): void
    {
    }
}