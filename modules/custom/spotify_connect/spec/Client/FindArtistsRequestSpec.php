<?php

namespace spec\Drupal\spotify_connect\Client;

use Drupal\spotify_connect\Client\FindArtistsRequest;
use PhpSpec\ObjectBehavior;

class FindArtistsRequestSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(FindArtistsRequest::class);
  }

  function it_creates_a_search_query_request() {
    $this->beConstructedThrough('bySearchQuery', [
      'q' => 'sub focus',
    ]);

    $this->getQuery()->shouldBe('limit=20&q=sub+focus&type=artist');
  }

}
