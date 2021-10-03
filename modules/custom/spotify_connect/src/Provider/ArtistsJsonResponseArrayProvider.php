<?php

namespace Drupal\spotify_connect\Provider;

use Drupal\spotify_connect\Client\Response\Artists;

class ArtistsJsonResponseArrayProvider {

  public function get(Artists $artists): array {
    $results = [];

    foreach ($artists->getArtists() as $artist) {
      $label = sprintf('%s - %s', $artist->getName(), $artist->getId());
      $results[] = [
        'value' => $label,
        'label' => $label,
      ];
    }

    return $results;
  }

}
