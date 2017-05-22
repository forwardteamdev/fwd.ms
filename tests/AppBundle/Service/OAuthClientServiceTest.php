<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 22/05/2017
 * Time: 11:56
 */

namespace Tests\AppBundle\Service;

use AppBundle\Document\Client as DocumentClient;
use AppBundle\Service\OAuthClientService;
use FOS\OAuthServerBundle\Document\Client;
use FOS\OAuthServerBundle\Document\ClientManager;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Prophecy\Prophet;

class OAuthClientServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Prophet
     */
    private $prophet;

    public function setUp()
    {
        $this->prophet = new Prophet();
    }

    public function testOAuthClientServiceClassExists()
    {
        $classExists = class_exists(OAuthClientService::class);
        $this->assertTrue($classExists);
    }

    public function testItCanCreateOAuthClient()
    {
        $client = $this->prophet->prophesize(Client::class);
        $clientManager = $this->prophet->prophesize(ClientManager::class);

        $clientManager->createClient()->willReturn($client);
        $clientManager->updateClient($client)->willReturn(null);

        $service = new OAuthClientService($clientManager->reveal());
        $oAuthClient = $service->create(['http://localhost']);

        $this->assertInstanceOf(ClientInterface::class, $oAuthClient);
    }
}
