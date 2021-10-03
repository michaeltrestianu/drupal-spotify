<?php

namespace Drupal\spotify_connect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase {

  private const BASE_URI_FORM_KEY = 'base_uri';
  private const AUTH_URI_FORM_KEY = 'auth_uri';
  private const CLIENT_SECRET_FORM_KEY = 'client_secret';
  private const CLIENT_ID_FORM_KEY = 'client_id';

  public function getFormId() {
    return 'spotify_admin_settings_form';
  }

  protected function getEditableConfigNames() {
    return [
      'spotify.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $spotify_config = $this->config('spotify.settings');

    $form[self::CLIENT_ID_FORM_KEY] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client id'),
      '#required' => TRUE,
      '#description' => $this->t('Enter the client secret from your spotify developer console'),
      '#default_value' => $spotify_config->get(self::CLIENT_ID_FORM_KEY) ?: '',
    ];

    $form[self::CLIENT_SECRET_FORM_KEY] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Client secret'),
      '#description' => $this->t('Enter the client secret from your spotify developer console'),
      '#default_value' => $spotify_config->get(self::CLIENT_SECRET_FORM_KEY) ?: '',
    ];

    $form[self::AUTH_URI_FORM_KEY] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Auth uri'),
      '#description' => $this->t('For example https://accounts.spotify.com/api/token'),
      '#default_value' => $spotify_config->get(self::AUTH_URI_FORM_KEY) ?: 'https://accounts.spotify.com/api/token',
    ];

    $form[self::BASE_URI_FORM_KEY] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Base uri'),
      '#description' => $this->t('For example https://api.spotify.com/v1/'),
      '#default_value' => $spotify_config->get(self::BASE_URI_FORM_KEY) ?: 'https://api.spotify.com/v1/',
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('spotify.settings')
      ->set('client_id', $values[self::CLIENT_ID_FORM_KEY])
      ->set('client_secret', $values[self::CLIENT_SECRET_FORM_KEY])
      ->set('auth_uri', $values[self::AUTH_URI_FORM_KEY])
      ->set('base_uri', $values[self::BASE_URI_FORM_KEY])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
