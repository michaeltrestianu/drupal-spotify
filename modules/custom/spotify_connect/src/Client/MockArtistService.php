<?php

namespace Drupal\spotify_connect\Client;

use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\Data\Image;

class MockArtistService implements ArtistServiceInterface {

  public function getArtistById(string $artistId): ?Artist {

    if ($artistId === '1234') {
      $images = [
        new Image(200, 200, ''),
      ];

      return new Artist('High Contrast', '1234', ['drum & bass'], $images);
    }

    return NULL;
  }

}
