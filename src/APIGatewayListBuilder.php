<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of APIGateway entities.
 *
 * @ingroup api_publisher
 */
class APIGatewayListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('APIGateway ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\api_publisher\Entity\APIGateway $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.api_gateway.edit_form',
      ['api_gateway' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
