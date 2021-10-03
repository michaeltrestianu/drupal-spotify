<?php

namespace Drupal\spotify_connect\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\spotify_connect\Client\FindArtistsRequest;
use Drupal\spotify_connect\Provider\ArtistsJsonResponseArrayProvider;
use Drupal\spotify_connect\SpotifyConnectFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FindArtistsController extends ControllerBase {

  private SpotifyConnectFacade $spotifyConnectFacade;

  private ArtistsJsonResponseArrayProvider $artistsJsonResponseArrayProvider;

  public function __construct(
    SpotifyConnectFacade $spotifyConnectFacade,
    ArtistsJsonResponseArrayProvider $artistsJsonResponseArrayProvider
  ) {
    $this->spotifyConnectFacade = $spotifyConnectFacade;
    $this->artistsJsonResponseArrayProvider = $artistsJsonResponseArrayProvider;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('spotify_connect.facade'),
      $container->get('spotify_connect.artist_json_response_array.provider')
    );
  }

  public function find(Request $request): JsonResponse {

    if (!$searchQuery = $request->query->get('q')) {
      return $this->createJsonResponse([]);
    }

    $artists = $this->spotifyConnectFacade->findArtists(
      FindArtistsRequest::bySearchQuery(Xss::filter($searchQuery))
    );

    $results = $this->artistsJsonResponseArrayProvider->get($artists);

    return $this->createJsonResponse($results);
  }

  private function createJsonResponse($results): JsonResponse {
    return new JsonResponse($results);
  }

}
