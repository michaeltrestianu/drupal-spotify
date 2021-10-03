<?php

namespace Drupal\spotify_connect;

use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\FindArtistsRequest;
use Drupal\spotify_connect\Client\Response\Artists;

class SpotifyConnectFacade {

  private SpotifyConnectFactory $spotifyConnectFactory;

  public function __construct(SpotifyConnectFactory $spotifyConnectFactory) {
    $this->spotifyConnectFactory = $spotifyConnectFactory;
  }

  public function getArtistById(string $artistId): ?Artist {
    return $this->spotifyConnectFactory->createArtistService()->getArtistById($artistId);
  }

  public function findArtists(FindArtistsRequest $request): Artists {
    return $this->spotifyConnectFactory->createSearchService()->searchArtist($request);
  }

}
