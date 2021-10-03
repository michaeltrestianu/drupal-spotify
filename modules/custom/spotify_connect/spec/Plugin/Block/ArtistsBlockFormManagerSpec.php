<?php

namespace spec\Drupal\spotify_connect\Plugin\Block;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\spotify_connect\Plugin\Block\ArtistsBlockFormManager;
use PhpSpec\ObjectBehavior;

class ArtistsBlockFormManagerSpec extends ObjectBehavior {

  function it_is_initializable() {
    $this->shouldHaveType(ArtistsBlockFormManager::class);
  }

  function let(TranslationManager $translationManager) {
    $container = new ContainerBuilder();
    $container->set('string_translation', $translationManager->getWrappedObject());
    \Drupal::setContainer($container);
  }

  function it_adds_20_empty_artists_search_form_fields(
    TranslationManager $translationManager
  ) {
    $this->getFormElements([])->shouldHaveCount(20);
  }

  function it_sets_configuration_values_from_form_values() {
    $this->getConfigurationValues([
      'search_0' => 'Netsky',
      'search_1' => 'High Contrast',
      'search_10' => 'London Grammar',
    ])->shouldReturn([
      'search_0' => 'Netsky',
      'search_1' => 'High Contrast',
      'search_10' => 'London Grammar',
    ]);
  }

  function it_ignores_configuration_values_that_do_not_match_the_given_format() {
    $this->getConfigurationValues([
      'search_0' => 'Netsky',
      'search_1' => 'High Contrast',
      'invalid' => 'Invalid',
    ])->shouldReturn([
      'search_0' => 'Netsky',
      'search_1' => 'High Contrast',
    ]);
  }

  function it_ignores_more_than_20_values() {
    $this->getConfigurationValues([
      'search_0' => 'Netsky',
      'search_1' => 'High Contrast',
      'search_21' => 'Invalid',
    ])->shouldReturn([
      'search_0' => 'Netsky',
      'search_1' => 'High Contrast',
    ]);
  }

}
