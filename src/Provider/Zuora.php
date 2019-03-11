<?php

namespace zaporylie\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Zuora extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Sandbox domain
     *
     * @var string
     *
     * @see $mode
     */
    protected $sandboxDomain = 'https://rest.apisandbox.zuora.com';

    /**
     * Production domain
     *
     * @var string
     *
     * @see $mode
     */
    protected $domain = 'https://rest.zuora.com';

    /**
     * Connection mode.
     *
     * Defines which domain should be used - production or sandbox.
     *
     * @param string
     *  Enum - 'production' or 'sandbox'
     *
     * @var string
     */
    protected $mode = 'production';

    /**
     * @var string
     */
    protected $pathAccessToken = '/oauth/token';

    /**
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->assertRequiredOptions($options);

        $possible   = $this->getConfigurableOptions();
        $configured = array_intersect_key($options, array_flip($possible));

        foreach ($configured as $key => $value) {
            $this->$key = $value;
        }

        // Remove all options that are only used locally
        $options = array_diff_key($options, $configured);

        parent::__construct($options, $collaborators);
    }

    /**
     * Returns all options that can be configured.
     *
     * @return array
     */
    protected function getConfigurableOptions()
    {
        return array_merge($this->getRequiredOptions(), [
            'pathAccessToken',
            'domain',
            'sandboxDomain',
            'mode',
        ]);
    }

    /**
     * Returns all options that are required.
     *
     * @return array
     */
    protected function getRequiredOptions()
    {
        return [];
    }

    /**
     * Verifies that all required options have been passed.
     *
     * @param  array $options
     * @return void
     * @throws \InvalidArgumentException
     */
    private function assertRequiredOptions(array $options)
    {
        $missing = array_diff_key(array_flip($this->getRequiredOptions()), $options);

        if (!empty($missing)) {
            throw new \InvalidArgumentException(
                'Required options not defined: ' . implode(', ', array_keys($missing))
            );
        }
    }

    /**
     * Only one grant is currently supported so set it as default.
     *
     * {@inheritdoc}
     */
    public function getAccessToken($grant = 'client_credentials', array $options = [])
    {
        return parent::getAccessToken($grant, $options);
    }

    /**
     * Not supported by Zuora provider.
     *
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        throw new \RuntimeException('Resource owner is not supported');
    }

    /**
     * Not supported by Zuora provider.
     *
     * {@inheritdoc}
     */
    public function getBaseAuthorizationUrl()
    {
        throw new \RuntimeException('Authorization is not supported');
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        $baseUrl = $this->mode === 'production' ? $this->domain : $this->sandboxDomain;
        return rtrim($baseUrl, '/') . '/' . ltrim($this->pathAccessToken, '/');
    }

    /**
     * Not supported by Zuora provider.
     *
     * {@inheritdoc}
     */
    protected function getDefaultScopes()
    {
        throw new \RuntimeException('Authorization is not supported');
    }

    /**
     * {@inheritdoc}
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response->getBody()->getContents()
            );
        }
    }

    /**
     * Not supported by Zuora provider.
     *
     * {@inheritdoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        throw new \RuntimeException('Resource owner is not supported');
    }
}
