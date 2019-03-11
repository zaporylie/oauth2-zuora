<?php

namespace zaporylie\OAuth2\Client\Test\Provider;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use zaporylie\OAuth2\Client\Provider\Zuora;

class ZuoraTest extends TestCase
{

    /**
     * @var \zaporylie\OAuth2\Client\Provider\Zuora
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Zuora([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
        ]);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);
        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $response = $this->createMock('Psr\Http\Message\ResponseInterface');
        $response->method('getBody')->willReturn('{"access_token": "mocked_access_token","token_type": "bearer","expires_in": 3599,"scope": "user.123 entity.456 entity.789 service.echo.read tenant.100","jti": "jti_token"}');
        $response->method('getHeader')->willReturn(['content-type' => 'json']);
        $response->method('getStatusCode')->willReturn(200);
        $client = $this->createMock('GuzzleHttp\ClientInterface');
        $client->method('send')->willReturn($response);
        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('client_credentials');
        $this->assertEquals('mocked_access_token', $token->getToken());
        $this->assertNotNull($token->getExpires());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Authorization is not supported
     */
    public function testAuthorizationUrl()
    {
        $this->provider->getAuthorizationUrl();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Resource owner is not supported
     */
    public function testResourceOwner()
    {
        $this->provider->getResourceOwner(new AccessToken(['access_token' => 'mocked_access_token']));
    }

}
