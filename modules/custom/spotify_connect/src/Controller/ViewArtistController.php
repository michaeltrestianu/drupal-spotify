<?php

namespace Drupal\spotify_connect\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\spotify_connect\SpotifyConnectFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ViewArtistController extends ControllerBase {

  private SpotifyConnectFacade $spotifyConnectFacade;

  public function __construct(
    SpotifyConnectFacade $spotifyConnectFacade
  ) {
    $this->spotifyConnectFacade = $spotifyConnectFacade;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('spotify_connect.facade')
    );
  }

  public function view(Request $request) {

    $artistId = $request->get('artist_id');

    $artist = $this->spotifyConnectFacade->getArtistById($artistId);

    if (!$artist) {
      return [
        '#markup' => $this->t('artist with id: @id not found', [
          '@id' => $artistId,
        ]),
      ];
    }

    return [
      '#theme' => 'artist',
      '#name' => $artist->getName(),
      '#id' => $artist->getId(),
      '#genres' => $artist->getGenres(),
      '#images' => $artist->getImages(),
    ];
  }

}
