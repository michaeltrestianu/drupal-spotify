<?php

namespace Drupal\spotify_connect\Provider;

class HtmlArtistListProvider {

  private ArtistLinkProvider $artistLinkProvider;

  private const CONFIG_PATTERN = '-';

  public function __construct(ArtistLinkProvider $artistLinkProvider) {
    $this->artistLinkProvider = $artistLinkProvider;
  }

  public function get(array $configurationItems): array {
    $items = [];
    foreach ($configurationItems as $key => $configurationItem) {

      if (!strpos($configurationItem, self::CONFIG_PATTERN)) {
        continue;
      }

      $artistParts = explode(self::CONFIG_PATTERN, $configurationItem);

      if (count($artistParts) !== 2) {
        throw new \RuntimeException('unexpected artist config item');
      }

      $items[$key] = $this->artistLinkProvider->get(
        trim($artistParts[0]),
        trim($artistParts[1])
      )->toRenderable();
    }

    return $items;
  }

}
