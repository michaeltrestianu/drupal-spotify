<?php

namespace Drupal\spotify_connect\Client;

use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\Response\Artists;

class MockSearchService implements SearchServiceInterface {

  public function searchArtist(FindArtistsRequest $request): Artists {
    return new Artists([
      new Artist('Sub Focus', '1234', ['electronic'], []),
    ], 1);
  }

}
