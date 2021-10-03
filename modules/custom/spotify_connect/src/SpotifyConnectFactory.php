<?php

namespace Drupal\spotify_connect;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\spotify_connect\Client\ArtistService;
use Drupal\spotify_connect\Client\ArtistServiceInterface;
use Drupal\spotify_connect\Client\MockArtistService;
use Drupal\spotify_connect\Client\MockSearchService;
use Drupal\spotify_connect\Client\SearchService;
use Drupal\spotify_connect\Client\AuthService;
use Drupal\spotify_connect\Client\SearchServiceInterface;
use Drupal\spotify_connect\Provider\BearerTokenProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class SpotifyConnectFactory {

  private LoggerChannelFactoryInterface $loggerChannelFactory;

  private PrivateTempStoreFactory $privateTempStoreFactory;

  private ConfigFactoryInterface $config;

  public function __construct(
    LoggerChannelFactoryInterface $loggerChannelFactory,
    PrivateTempStoreFactory $privateTempStoreFactory,
    ConfigFactoryInterface $config
  ) {

    $this->loggerChannelFactory = $loggerChannelFactory;
    $this->privateTempStoreFactory = $privateTempStoreFactory;
    $this->config = $config;
  }

  public function createArtistService(): ArtistServiceInterface {

    if ($this->isInTestMode()) {
      return new MockArtistService();
    }

    return new ArtistService(
      $this->createClient(),
      $this->loggerChannelFactory,
      $this->createAuthService(),
      $this->createBearerTokenProvider()
    );
  }

  public function createSearchService(): SearchServiceInterface {

    if ($this->isInTestMode()) {
      return new MockSearchService();
    }

    return new SearchService(
      $this->createClient(),
      $this->loggerChannelFactory,
      $this->createAuthService(),
      $this->createBearerTokenProvider()
    );
  }

  public function createBearerTokenProvider(): BearerTokenProvider {
    return new BearerTokenProvider($this->privateTempStoreFactory);
  }

  public function createAuthService(): AuthService {
    return new AuthService(
      $this->createClient(),
      $this->privateTempStoreFactory,
      $this->config
    );
  }

  public function createClient(): ClientInterface {
    return new Client([
      'http_errors' => FALSE,
      'base_uri' => $this->config->get('spotify.settings')->get('base_uri'),
    ]);
  }

  private function isInTestMode() : bool {
    return !empty($this->config->getEditable('app.testmode')->get('devtest'));
  }

}
