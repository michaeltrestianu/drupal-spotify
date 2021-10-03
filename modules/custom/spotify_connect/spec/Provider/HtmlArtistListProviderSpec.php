<?php

namespace spec\Drupal\spotify_connect\Provider;

use Drupal\Core\Link;
use Drupal\spotify_connect\Provider\ArtistLinkProvider;
use Drupal\spotify_connect\Provider\HtmlArtistListProvider;
use PhpSpec\ObjectBehavior;

class HtmlArtistListProviderSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(HtmlArtistListProvider::class);
  }

  function let(ArtistLinkProvider $artistLinkProvider) {
    $this->beConstructedWith($artistLinkProvider);
  }

  function it_skips_config_items_that_are_not_in_the_expected_format() {
    $this->get(['12345', 'tes_config_item'])->shouldBe([]);
  }

  function it_generates_links_for_valid_config_items(
    ArtistLinkProvider $artistLinkProvider,
    Link $link
  ) {
    $link->toRenderable()->willReturn(['render array']);
    $artistLinkProvider->get('sub focus', '12345')->willReturn($link);
    $this->get(['sub focus - 12345'])->shouldBeLike([
      [
        'render array',
      ],
    ]);
  }

  function it_throws_an_exception_if_a_config_item_is_not_in_the_expected_format(
    ArtistLinkProvider $artistLinkProvider,
    Link $link
  ) {
    $artistLinkProvider->get('sub focus', '12345')->willReturn($link);
    $this->shouldThrow(\RuntimeException::class)->duringGet(
      ['sub focus - 12345 - deep space']
    );
  }

}
