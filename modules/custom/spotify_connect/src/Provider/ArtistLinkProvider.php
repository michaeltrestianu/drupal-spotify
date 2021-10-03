<?php

namespace Drupal\spotify_connect\Provider;

use Drupal\Core\Link;
use Drupal\Core\Url;

class ArtistLinkProvider {

  public function get(string $name, string $artistId): Link {
    $url = Url::fromRoute('spotify_connect.view.artist', [
      'artist_id' => $artistId,
    ]);

    return Link::fromTextAndUrl($name, $url);
  }

}
