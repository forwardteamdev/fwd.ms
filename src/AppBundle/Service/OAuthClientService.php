<?php

/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 21/05/2017
 * Time: 22:09
 */

namespace AppBundle\Service;

use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;

class OAuthClientService
{
    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    /**
     * @param array $redirectUri
     * @return ClientInterface
     */
    public function create(array $redirectUri) : ClientInterface
    {
        $client = $this->clientManager->createClient();
        $client->setRedirectUris($redirectUri);
        $client->setAllowedGrantTypes(['password']);
        $this->clientManager->updateClient($client);

        return $client;
    }
}
