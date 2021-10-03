<?php

namespace Drupal\spotify_connect\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;

class BearerTokenProvider {

  private PrivateTempStoreFactory $privateTempStoreFactory;

  public function __construct(
    PrivateTempStoreFactory $privateTempStoreFactory
  ) {
    $this->privateTempStoreFactory = $privateTempStoreFactory;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private')
    );
  }

  public function get(): ?string {
    return $this->privateTempStoreFactory->get('spotify_connect')->get('access_token');
  }

}
