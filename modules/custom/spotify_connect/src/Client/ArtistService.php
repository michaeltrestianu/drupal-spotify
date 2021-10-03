<?php

namespace Drupal\spotify_connect\Client;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\Data\Image;
use Drupal\spotify_connect\Client\Response\Exception\InvalidResponseException;
use Drupal\spotify_connect\Provider\BearerTokenProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\HttpFoundation\Request;

class ArtistService implements ArtistServiceInterface {

  private const GET_ARTIST_ENDPOINT_PATTERN = 'artists/%s';

  private ClientInterface $client;

  private LoggerChannelInterface $loggerChannel;

  private AuthService $authService;

  private BearerTokenProvider $bearerTokenProvider;

  public function __construct(
    ClientInterface $client,
    LoggerChannelFactoryInterface $loggerChannelFactory,
    AuthService $authService,
    BearerTokenProvider $bearerTokenProvider
  ) {
    $this->client = $client;
    $this->loggerChannel = $loggerChannelFactory->get('spotify_service');
    $this->authService = $authService;
    $this->bearerTokenProvider = $bearerTokenProvider;
  }

  public function getArtistById(string $artistId): ?Artist {

    $bearerToken = $this->bearerTokenProvider->get();

    try {
      if (empty($bearerToken)) {
        $this->authService->authenticate();
        return $this->getArtistById($artistId);
      }

      $response = $this->client->request(
        Request::METHOD_GET,
        sprintf(self::GET_ARTIST_ENDPOINT_PATTERN, $artistId),
        [
          'headers' => [
            'Authorization' => sprintf('Bearer %s', $bearerToken),
          ],
        ]
      );

      if ($response->getStatusCode() === 401) {
        $this->authService->authenticate();
        return $this->getArtistById($artistId);
      }

      if ($response->getStatusCode() !== 200) {
        throw new InvalidResponseException(
          sprintf(
            'Non 200 response received (%s), please check request or connection.',
            $response->getStatusCode()
          )
        );
      }
    }
    catch (ServerException | InvalidResponseException | \RuntimeException $e) {
      $this->loggerChannel->error(
        'An error occurred fetching artists data %error',
        [
          '%error' => $e->getMessage() . sprintf(
              self::GET_ARTIST_ENDPOINT_PATTERN,
              $artistId
          ),
        ],
      );

      return NULL;
    }

    $body = json_decode($response->getBody()->getContents());

    if (!$body) {
      $this->loggerChannel->error(
        'Unable to decode response body when requesting artist'
      );

      return NULL;
    }

    $response->getBody()->close();

    $images = [];
    foreach ($body->images ?? [] as $image) {
      $images[] = new Image($image->height, $image->width, $image->url);
    }

    return new Artist($body->name, $body->id, $body->genres ?? [], $images);
  }

}
