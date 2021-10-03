<?php

namespace Drupal\spotify_connect\Client;

class FindArtistsRequest implements FindArtistRequestInterface {

  private string $searchQuery;

  private string $type;

  private string $limit;

  private const TYPE = 'artist';

  public static function bySearchQuery(string $searchQuery): self {
    $request = new self();
    $request->setSearchQuery($searchQuery);
    $request->setLimit(20);
    $request->setType(self::TYPE);

    return $request;
  }

  public function getQuery(): string {
    $query = [];

    if ($this->limit !== NULL) {
      $query['limit'] = $this->limit;
    }

    if ($this->searchQuery !== NULL) {
      $query['q'] = $this->searchQuery;
    }

    if ($this->type !== NULL) {
      $query['type'] = $this->type;
    }

    return http_build_query($query);
  }

  private function setType(string $type): void {
    $this->type = $type;
  }

  private function setLimit(string $limit): void {
    $this->limit = $limit;
  }

  private function setSearchQuery(string $searchQuery): void {
    $this->searchQuery = $searchQuery;
  }

  private function __construct() {
  }

}
