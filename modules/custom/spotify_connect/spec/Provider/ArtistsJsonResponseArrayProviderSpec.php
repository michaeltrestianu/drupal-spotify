<?php

namespace spec\Drupal\spotify_connect\Provider;

use Drupal\spotify_connect\Client\Data\Artist;
use Drupal\spotify_connect\Client\Response\Artists;
use Drupal\spotify_connect\Provider\ArtistsJsonResponseArrayProvider;
use PhpSpec\ObjectBehavior;

class ArtistsJsonResponseArrayProviderSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(ArtistsJsonResponseArrayProvider::class);
  }

  function it_returns_an_array_of_results(Artists $artists, Artist $artist) {
    $artist->getName()->willReturn('High Contrast');
    $artist->getId()->willReturn('123456');
    $artists->getArtists()->willReturn([$artist]);
    $this->get($artists)->shouldBe([
      [
        'value' => 'High Contrast - 123456',
        'label' => 'High Contrast - 123456',
      ],
    ]);
  }

  function it_returns_an_empty_array_if_there_are_no_artists(Artists $artists) {
    $artists->getArtists()->willReturn([]);
    $this->get($artists)->shouldBe([]);
  }

}
