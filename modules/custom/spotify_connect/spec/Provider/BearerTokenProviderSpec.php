<?php

namespace spec\Drupal\spotify_connect\Provider;

use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\spotify_connect\Provider\BearerTokenProvider;
use PhpSpec\ObjectBehavior;

class BearerTokenProviderSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(BearerTokenProvider::class);
  }

  function let(PrivateTempStoreFactory $privateTempStoreFactory) {
    $this->beConstructedWith($privateTempStoreFactory);
  }

  function it_gets_the_bearer_token_from_the_private_temp_store(
    PrivateTempStoreFactory $privateTempStoreFactory,
    PrivateTempStore $privateTempStore
  ) {
    $privateTempStore->get('access_token')->willReturn('125678');
    $privateTempStoreFactory->get('spotify_connect')->willReturn($privateTempStore);
    $this->get()->shouldBe('125678');
  }

}
