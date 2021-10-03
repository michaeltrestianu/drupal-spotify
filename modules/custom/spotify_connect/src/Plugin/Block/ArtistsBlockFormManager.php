<?php

namespace Drupal\spotify_connect\Plugin\Block;

use Drupal\Core\StringTranslation\StringTranslationTrait;

class ArtistsBlockFormManager {

  use StringTranslationTrait;

  public const ARTIST_LIMIT = 20;

  public const ARTIST_FORM_KEY_PATTERN = 'search_%d';

  public function getFormElements(array $config): array {
    $formElements = [];
    for ($i = 0; $i < self::ARTIST_LIMIT; $i++) {
      $formKey = $this->generateFormKey($i);

      $formElements[$formKey] = [
        '#type' => 'textfield',
        '#title' => $this->t('Search for artist: @count', ['@count' => $i + 1]),
        '#autocomplete_route_name' => 'spotify_connect.autocomplete.artists',
        '#default_value' => $config[$formKey] ?? '',
      ];
    }

    return $formElements;
  }

  public function getConfigurationValues(array $formValues): array {
    $configurationValues = [];
    for ($i = 0; $i < self::ARTIST_LIMIT; $i++) {
      $formKey = $this->generateFormKey($i);
      if (!empty($formValues[$formKey])) {
        $configurationValues[$formKey] = $formValues[$formKey];
      }
    }
    return $configurationValues;
  }

  private function generateFormKey(int $item): string {
    return sprintf(self::ARTIST_FORM_KEY_PATTERN, $item);
  }

}
