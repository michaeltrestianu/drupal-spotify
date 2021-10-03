<?php

namespace Drupal\spotify_connect\Client\Data;

class Artist {

  private string $name;

  private string $id;

  private array $genres;

  /**
   * @var \Drupal\spotify_connect\Client\Data\Image[]
   */
  private array $images;

  public function __construct(string $name, string $id, array $genres, array $images) {
    $this->name = $name;
    $this->id = $id;
    $this->genres = $genres;
    $this->images = $images;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getId(): string {
    return $this->id;
  }

  public function getGenres(): array {
    return $this->genres;
  }

  public function getImages(): array {
    return $this->images;
  }

}
