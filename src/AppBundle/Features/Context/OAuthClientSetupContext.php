<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 15/05/2017
 * Time: 16:19
 */

namespace AppBundle\Features\Context;

use AppBundle\Service\OAuthClientService;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class OAuthClientSetupContext implements Context, SnippetAcceptingContext
{
    /**
     * @var ClientManagerInterface
     */
    private $clientService;

    /**
     * @var Session
     */
    private $session;

    /**
     * UserSetupContext constructor.
     * @param OAuthClientService $clientService
     */
    public function __construct(OAuthClientService $clientService, Session $session)
    {
        $this->clientService = $clientService;
        $this->session = $session;
    }

    /**
     * @Given there are oAuth Client with the following details:
     * @param TableNode $clients
     */
    public function thereIsOAuthClientWithFollowingDetails(TableNode $clients)
    {
        $uri = [];

        foreach ($clients->getColumnsHash() as $key => $val) {
            $uri[] = isset($val['redirect_uri']) && $val['redirect_uri'] !== ''
                ? $val['redirect_uri']
                : '';
        }

        $client = $this->clientService->create($uri);
        $this->session->set('oauth_client', $client);
    }
}
