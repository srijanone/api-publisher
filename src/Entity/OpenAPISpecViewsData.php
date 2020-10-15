<?php

namespace Drupal\api_publisher\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for OpenAPI Specification entities.
 */
class OpenAPISpecViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
