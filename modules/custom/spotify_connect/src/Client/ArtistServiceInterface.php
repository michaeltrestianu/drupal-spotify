<?php

namespace Drupal\spotify_connect\Client;

use Drupal\spotify_connect\Client\Data\Artist;

interface ArtistServiceInterface {

  public function getArtistById(string $artistId): ?Artist;

}
