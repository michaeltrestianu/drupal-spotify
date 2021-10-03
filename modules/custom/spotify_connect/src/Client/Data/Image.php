<?php

namespace Drupal\spotify_connect\Client\Data;

class Image {

  private string $url;

  private int $height;

  private int $width;

  public function __construct(int $height, int $width, string $url) {
    $this->height = $height;
    $this->width = $width;
    $this->url = $url;
  }

  public function getUrl(): string {
    return $this->url;
  }

  public function getHeight(): int {
    return $this->height;
  }

  public function getWidth(): int {
    return $this->width;
  }

}
