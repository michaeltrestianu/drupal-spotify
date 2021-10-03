<?php

namespace Drupal\spotify_connect\Client;

use Drupal\spotify_connect\Client\Response\Artists;

interface SearchServiceInterface {

  public function searchArtist(FindArtistsRequest $request): Artists;

}
