<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of OpenAPI Specification entities.
 *
 * @ingroup api_publisher
 */
class OpenAPISpecListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('OpenAPI Specification ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\api_publisher\Entity\OpenAPISpec $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.open_api_spec.edit_form',
      ['open_api_spec' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
