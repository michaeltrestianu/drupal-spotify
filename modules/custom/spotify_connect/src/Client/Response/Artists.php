<?php

namespace Drupal\spotify_connect\Client\Response;

class Artists {

  private array $artists;

  private int $total;

  /**
   * @param \Drupal\spotify_connect\Client\Data\Artist[] $artists
   * @param int $total
   */
  public function __construct(array $artists, int $total) {

    $this->artists = $artists;
    $this->total = $total;
  }

  public function getTotal(): int {
    return $this->total;
  }

  public function getArtists(): array {
    return $this->artists;
  }

}
