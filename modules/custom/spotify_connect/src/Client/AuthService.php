<?php

namespace Drupal\spotify_connect\Client;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\spotify_connect\Client\Response\Exception\InvalidResponseException;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthService {

  private const GRANT_TYPE = 'client_credentials';

  private ClientInterface $client;

  private PrivateTempStoreFactory $privateTempStoreFactory;

  private ImmutableConfig $config;

  public function __construct(
    ClientInterface $client,
    PrivateTempStoreFactory $privateTempStoreFactory,
    ConfigFactoryInterface $config
  ) {
    $this->client = $client;
    $this->privateTempStoreFactory = $privateTempStoreFactory;
    $this->config = $config->get('spotify.settings');
  }

  public function authenticate(): void {
    $response = $this->client->request(
      Request::METHOD_POST,
      $this->config->get('auth_uri'),
      [
        'headers' => [
          'Authorization' => sprintf(
            'Basic %s',
            base64_encode(
              sprintf(
                '%s:%s',
                $this->config->get('client_id'),
                $this->config->get('client_secret')
              )
            )
          ),
          'Content-Type' => 'application/x-www-form-urlencoded',
        ],
        'form_params' => [
          'grant_type' => self::GRANT_TYPE,
        ],
      ]
    );

    if ($response->getStatusCode() !== Response::HTTP_OK) {
      throw new InvalidResponseException(
        sprintf(
          'Non 200 response received (%s), please check request or connection.',
          $response->getStatusCode()
        )
      );
    }

    $body = json_decode($response->getBody()->getContents());

    if (!$body) {
      throw new \RuntimeException(
        'Unable to decode response body when authenticating'
      );
    }

    $this->privateTempStoreFactory->get('spotify_connect')->set(
      'access_token',
      $body->access_token
    );

    $response->getBody()->close();
  }

}
