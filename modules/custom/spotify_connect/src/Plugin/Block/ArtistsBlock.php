<?php

namespace Drupal\spotify_connect\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\spotify_connect\Provider\HtmlArtistListProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Block(
 *   id = "spotify_artists",
 *   admin_label = @Translation("Spotify artists"),
 *   category = @Translation("Spotify"),
 * )
 */
class ArtistsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  private HtmlArtistListProvider $htmlArtistListProvider;

  /**
   * @var \Drupal\spotify_connect\Plugin\Block\ArtistsBlockFormManager
   */
  private ArtistsBlockFormManager $artistsBlockFormManager;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    HtmlArtistListProvider $htmlArtistListProvider,
    ArtistsBlockFormManager $artistsBlockFormManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->htmlArtistListProvider = $htmlArtistListProvider;
    $this->artistsBlockFormManager = $artistsBlockFormManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('spotify_connect.html_artist_list.provider'),
      $container->get('spotify_connect.artist_block_form.manager')
    );
  }

  public function build() {
    return $this->htmlArtistListProvider->get(
      $this->getConfiguration()
    );
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $elements = $this->artistsBlockFormManager->getFormElements(
      $this->getConfiguration()
    );

    return $form + $elements;
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $configValues = $this->artistsBlockFormManager->getConfigurationValues(
      $form_state->getValues()
    );
    $this->setConfiguration($configValues);
  }

}
