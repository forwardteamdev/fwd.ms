<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 15/05/2017
 * Time: 16:23
 */

namespace AppBundle\Features\Context;

use AppBundle\Document\Client;
use AppBundle\Document\UserInvitation;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\ObjectManager;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Psr7;
use PHPUnit_Framework_Assert as Assertions;
use Sanpi\Behatch\Json\JsonInspector;
use Sanpi\Behatch\Json\JsonSchema;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class RestApiContext
 * @package AppBundle\Features\Context
 */
class RestApiContext implements Context
{
    const OAUTH_CLIENT_ID_PLACEHOLDER = '__OAUTH_CLIENT_ID__';
    const OAUTH_CLIENT_SECRET_PLACEHOLDER = '__OAUTH_CLIENT_SECRET__';
    const USER_INVITATION_CODE = '__USER_INVITATION_CODE__';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    private $authorization;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var \GuzzleHttp\Message\RequestInterface
     */
    private $request;

    /**
     * @var \GuzzleHttp\Message\ResponseInterface
     */
    private $response;

    /**
     * @var array
     */
    private $placeHolders = array();

    /**
     * @var Session
     */
    private $session;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var array
     */
    private $data;

    /**
     * RestApiContext constructor.
     * @param ClientInterface $client
     * @param Session $session
     * @param ObjectManager $objectManager
     */
    public function __construct(ClientInterface $client, Session $session, ObjectManager $objectManager)
    {
        $this->client = $client;
        $this->session = $session;
        $this->objectManager = $objectManager;
    }

    /**
     * Adds Basic Authentication header to next request.
     *
     * @param string $username
     * @param string $password
     *
     * @Given /^I am authenticating as "([^"]*)" with "([^"]*)" password$/
     */
    public function iAmAuthenticatingAs($username, $password)
    {
        $this->removeHeader('Authorization');

        try {
            $this->iSendARequest('POST', '/app_acceptance.php/oauth/v2/token', [
                'json' => [
                    'grant_type' => 'password',
                    'client_id' => $this->getOAuthClientData('getPublicId'),
                    'client_secret' => $this->getOAuthClientData('getSecret'),
                    'username' => $username,
                    'password' => $password,
                ]
            ]);

            $this->theResponseCodeShouldBe(200);

            $responseBody = json_decode($this->response->getBody(), true);
            $this->addHeader('Authorization', 'Bearer ' . $responseBody['access_token']);

        } catch (RequestException $e) {
            echo Psr7\str($e->getRequest());

            if ($e->hasResponse()) {
                echo Psr7\str($e->getResponse());
            }

        }
    }

    /**
     * Adds oAuth Token to Authentication header for next request
     *
     * @param string $username
     * @param string $password
     *
     * @Given /^I am successfully logged in with username: "([^"]*)", and password: "([^"]*)"$/
     */
    public function iAmSuccessfullyLoggedInWithUsernameAndPassword($username, $password)
    {
        try {
            $this->iSendARequest('POST', '/app_acceptance.php/oauth/v2/token', [
                'json' => [
                    'grant_type' => 'password',
                    'client_id' => $this->getOAuthClientData('getPublicId'),
                    'client_secret' => $this->getOAuthClientData('getSecret'),
                    'username' => $username,
                    'password' => $password,
                ]
            ]);

            $this->theResponseCodeShouldBe(200);

            $responseBody = json_decode($this->response->getBody(), true);
            $this->addHeader('Authorization', 'Bearer ' . $responseBody['access_token']);

        } catch (RequestException $e) {
            echo Psr7\str($e->getRequest());

            if ($e->hasResponse()) {
                echo Psr7\str($e->getResponse());
            }

        }
    }

    /**
     * Get User Invitation Code by Email
     * @param $email
     *
     * @When /^I have user invitation code for email: "([^"]*)"$/
     */
    public function IHaveUserInvitationCode($email)
    {
        $invitation = $this->getUserInvitationCode($email);
        $this->data['invitation'] =  ($invitation instanceof UserInvitation) ? $invitation->getCode() : '';
    }

    /**
     * @When I have forgotten to set the :header
     */
    public function iHaveForgottenToSetThe($header)
    {
        $this->addHeader($header, null);
    }

    /**
     * Sets a HTTP Header.
     *
     * @param string $name  header name
     * @param string $value header value
     *
     * @Given /^I set header "([^"]*)" with value "([^"]*)"$/
     */
    public function iSetHeaderWithValue($name, $value)
    {
        $this->addHeader($name, $value);
    }

    /**
     * Sends HTTP request to specific relative URL.
     *
     * @param string $method request method
     * @param string $url    relative url
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)"$/
     */
    public function iSendARequest($method, $url, array $data = [])
    {
        $url = $this->prepareUrl($url);
        $data = $this->prepareData($data);

        try {
            $this->response = $this->getClient()->request($method, $url, $data);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $this->response = $e->getResponse();
            }
        }
    }

    /**
     * Sends HTTP request to specific URL with field values from Table.
     *
     * @param string $method request method
     * @param string $url relative url
     * @param TableNode $post table of post values
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with values:$/
     * @throws \RuntimeException
     */
    public function iSendARequestWithValues($method, $url, TableNode $post)
    {
        $url = $this->prepareUrl($url);
        $fields = array();

        foreach ($post->getRowsHash() as $key => $val) {
            if ($key === 'client_id') {
                $fields[$key] = $this->getOAuthClientData('getPublicId');
            } elseif ($key === 'client_secret') {
                $fields[$key] = $this->getOAuthClientData('getSecret');
            } else {
                $fields[$key] = $this->replacePlaceHolder($val);
            }
        }

        $bodyOption = array(
            'body' => json_encode($fields),
        );
        $this->request = $this->getClient()->createRequest($method, $url, $bodyOption);
        if (!empty($this->headers)) {
            $this->request->addHeaders($this->headers);
        }

        $this->sendRequest();
    }

    /**
     * Sends HTTP request to specific URL with raw body from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)" with body:$/
     */
    public function iSendARequestWithBody($method, $url, PyStringNode $string)
    {
        $placeholders = [
            self::OAUTH_CLIENT_ID_PLACEHOLDER,
            self::OAUTH_CLIENT_SECRET_PLACEHOLDER,
            self::USER_INVITATION_CODE
        ];

        $oauthClientValues = [
            $this->getOAuthClientData('getPublicId'),
            $this->getOAuthClientData('getSecret'),
            $this->data['invitation']
        ];

        $string = str_replace($placeholders, $oauthClientValues, $string);

        $url = $this->prepareUrl($url);
        $string = $this->replacePlaceHolder(trim($string));

        $this->request = $this->iSendARequest(
            $method,
            $url,
            [ 'body' => $string ]
        );
    }

    /**
     * Sends HTTP request to specific URL with form data from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $body   request body
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)" with form data:$/
     */
    public function iSendARequestWithFormData($method, $url, PyStringNode $body)
    {
        $url = $this->prepareUrl($url);
        $body = $this->replacePlaceHolder(trim($body));

        $fields = array();
        parse_str(implode('&', explode("\n", $body)), $fields);
        $this->request = $this->getClient()->createRequest($method, $url);
        /** @var \GuzzleHttp\Post\PostBodyInterface $requestBody */
        $requestBody = $this->request->getBody();
        foreach ($fields as $key => $value) {
            $requestBody->setField($key, $value);
        }

        $this->sendRequest();
    }

    /**
     * @When /^(?:I )?send a multipart "([A-Z]+)" request to "([^"]+)" with form data:$/
     */
    public function iSendAMultipartRequestToWithFormData($method, $url, TableNode $post)
    {
        $url = $this->prepareUrl($url);

        $this->request = $this->getClient()->createRequest($method, $url);

        $data = $post->getColumnsHash()[0];

        $hasFile = false;

        if (array_key_exists('filePath', $data)) {
            $filePath = $this->dummyDataPath . $data['filePath'];
            unset($data['filePath']);
            $hasFile = true;
        }


        /** @var \GuzzleHttp\Post\PostBodyInterface $requestBody */
        $requestBody = $this->request->getBody();
        foreach ($data as $key => $value) {
            $requestBody->setField($key, $value);
        }


        if ($hasFile) {
            $file = fopen($filePath, 'rb');
            $postFile = new PostFile('uploadedFile', $file);
            $requestBody->addFile($postFile);
        }


        if (!empty($this->headers)) {
            $this->request->addHeaders($this->headers);
        }
        $this->request->setHeader('Content-Type', 'multipart/form-data');

        $this->sendRequest();
    }

    /**
     * Checks that response has specific status code.
     *
     * @param string $code status code
     *
     * @Then the response code should be :arg1
     */
    public function theResponseCodeShouldBe($code)
    {
        $expected = (int) $code;
        $actual = (int) $this->response->getStatusCode();
        Assertions::assertSame($expected, $actual);
    }

    /**
     * Checks that response body contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should contain "((?:[^"]|\\")*)"$/
     */
    public function theResponseShouldContain($text)
    {
        $expectedRegexp = '/' . preg_quote($text) . '/i';
        $actual = (string) $this->response->getBody();
        Assertions::assertRegExp($expectedRegexp, $actual);
    }

    /**
     * Checks that response body doesn't contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should not contain "([^"]*)"$/
     */
    public function theResponseShouldNotContain($text)
    {
        $expectedRegexp = '/' . preg_quote($text) . '/';
        $actual = (string) $this->response->getBody();
        Assertions::assertNotRegExp($expectedRegexp, $actual);
    }

    /**
     * Checks that response body contains JSON from PyString.
     *
     * Do not check that the response body /only/ contains the JSON from PyString,
     *
     * @param PyStringNode $jsonString
     *
     * @throws \RuntimeException
     *
     * @Then /^(?:the )?response should contain json:$/
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        $etalon = json_decode($this->replacePlaceHolder($jsonString->getRaw()), true);
        $actual = json_decode($this->response->getBody(), true);

        if (null === $etalon) {
            throw new \RuntimeException(
                "Can not convert etalon to json:\n" . $this->replacePlaceHolder($jsonString->getRaw())
            );
        }

        Assertions::assertGreaterThanOrEqual(count($etalon), count($actual));
        foreach ($etalon as $key => $needle) {
            Assertions::assertArrayHasKey($key, $actual);
            Assertions::assertEquals($etalon[$key], $actual[$key]);
        }
    }

    /**
     * Prints last response body.
     *
     * @Then print response
     */
    public function printResponse()
    {

        $response = $this->response;

        echo sprintf(
            "%d:\n%s",
            $response->getStatusCode(),
            $response->getBody()
        );
    }

    /**
     * @Then the response header :header should be equal to :value
     */
    public function theResponseHeaderShouldBeEqualTo($header, $value)
    {
        $header = $this->response->getHeaders()[$header];
        Assertions::assertContains($value, $header);
    }

    /**
     * Prepare URL by replacing placeholders and trimming slashes.
     *
     * @param string $url
     *
     * @return string
     */
    private function prepareUrl($url)
    {
        return ltrim($this->replacePlaceHolder($url), '/');
    }

    /**
     * Sets place holder for replacement.
     *
     * you can specify placeholders, which will
     * be replaced in URL, request or response body.
     *
     * @param string $key   token name
     * @param string $value replace value
     */
    public function setPlaceHolder($key, $value)
    {
        $this->placeHolders[$key] = $value;
    }

    /**
     * @Then I follow the link in the Location response header
     */
    public function iFollowTheLinkInTheLocationResponseHeader()
    {
        $location = $this->response->getHeader('Location')[0];

        $this->iSendARequest(Request::METHOD_GET, $location);
    }

    /**
     * @Then the JSON should be valid according to this schema:
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema)
    {
        $inspector = new JsonInspector('javascript');

        $json = new \Sanpi\Behatch\Json\Json(json_encode($this->response->json()));

        $inspector->validate(
            $json,
            new JsonSchema($schema)
        );
    }

    /**
     * Checks, that given JSON node is equal to given value
     *
     * @Then the JSON node :node should be equal to :text
     * @throws \Exception
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        $json = new \Sanpi\Behatch\Json\Json(json_encode($this->response->json()));

        $inspector = new JsonInspector('javascript');

        $actual = $inspector->evaluate($json, $node);

        if ($actual !== $text) {
            throw new \InvalidArgumentException(
                sprintf("The node value is '%s'", json_encode($actual))
            );
        }
    }

    /**
     * Replaces placeholders in provided text.
     *
     * @param string $string
     *
     * @return string
     */
    protected function replacePlaceHolder($string)
    {
        foreach ($this->placeHolders as $key => $val) {
            $string = str_replace($key, $val, $string);
        }

        return $string;
    }

    /**
     * Returns headers, that will be used to send requests.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Adds header
     *
     * @param string $name
     * @param string $value
     */
    protected function addHeader($name, $value)
    {
        if (! isset($this->headers[$name])) {
            $this->headers[$name] = $value;
        }

        if (!is_array($this->headers[$name])) {
            $this->headers[$name] = [$this->headers[$name]];
        }

        $this->headers[$name] = $value;
    }

    /**
     * Removes a header identified by $headerName
     *
     * @param string $headerName
     */
    protected function removeHeader($headerName)
    {
        if (array_key_exists($headerName, $this->headers)) {
            unset($this->headers[$headerName]);
        }
    }

    /**
     * @return ClientInterface
     * @throws \RuntimeException
     */
    private function getClient()
    {
        if (null === $this->client) {
            throw new \RuntimeException('Client has not been set in WebApiContext');
        }

        return $this->client;
    }

    private function prepareData($data)
    {
        if (!empty($this->headers)) {
            $data = array_replace(
                $data,
                ['headers' => $this->headers]
            );
        }

        return $data;
    }

    private function getOAuthClientData($property)
    {
        $client = $this->session->get('oauth_client');

        if (!$client instanceof Client) {
            return '';
        }

        return $client->$property();
    }

    private function getUserInvitationCode($email)
    {
        return $this
            ->objectManager
            ->getRepository(UserInvitation::class)
            ->findOneBy(['email' => $email]);
    }
}
