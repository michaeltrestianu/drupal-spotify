<?php

namespace Drupal\spotify_connect\Client;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\Response\Artists;
use Drupal\spotify_connect\Client\Response\Exception\InvalidResponseException;
use Drupal\spotify_connect\Provider\BearerTokenProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\HttpFoundation\Request;

class SearchService implements searchServiceInterface {

  private const SEARCH_ENDPOINT = 'search';

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

  public function searchArtist(FindArtistsRequest $request): Artists {

    if (empty($request->getQuery())) {
      return $this->createArtistsResponse([], 0);
    }

    $bearerToken = $this->bearerTokenProvider->get();

    try {
      if (empty($bearerToken)) {
        $this->authService->authenticate();
        return $this->searchArtist($request);
      }

      $response = $this->client->request(
        Request::METHOD_GET,
        self::SEARCH_ENDPOINT,
        [
          'query' => $request->getQuery(),
          'headers' => [
            'Authorization' => sprintf('Bearer %s', $bearerToken),
          ],
        ]
      );

      if ($response->getStatusCode() === 401) {
        $this->authService->authenticate();
        return $this->searchArtist($request);
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
          '%error' => $e->getMessage(),
        ],
      );

      return $this->createArtistsResponse([], 0);
    }

    $body = json_decode($response->getBody()->getContents());

    if (!$body) {
      $this->loggerChannel->error(
        'Unable to decode response body when requesting artists'
      );

      return $this->createArtistsResponse([], 0);
    }

    $responseArtists = [];
    foreach ($body->artists->items ?? [] as $artist) {
      $responseArtists[] = new Artist($artist->name, $artist->id, [], []);
    }

    $response->getBody()->close();
    return $this->createArtistsResponse($responseArtists, $body->artists->total);
  }

  private function createArtistsResponse(array $artists, int $count): Artists {
    return new Artists($artists, $count);
  }

}
