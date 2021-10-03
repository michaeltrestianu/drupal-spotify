<?php

namespace Acceptance;

use Drupal\DrupalExtension\Context\RawDrupalContext;

class Context extends RawDrupalContext {

  /**
   * @BeforeSuite
   */
  public static function beforeSuite() {
    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config */
    $config = \Drupal::service('config.factory');
    $config->getEditable('app.testmode')->set('devtest', true)->save();
  }

  /**
   * @AfterSuite
   */
  public static function afterSuite() {
    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config */
    $config = \Drupal::service('config.factory');
    $config->getEditable('app.testmode')->delete();
  }
}
