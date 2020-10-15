<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\api_publisher\Entity\APIGatewayInterface;

/**
 * Defines the storage handler class for APIGateway entities.
 *
 * This extends the base storage class, adding required special handling for
 * APIGateway entities.
 *
 * @ingroup api_publisher
 */
class APIGatewayStorage extends SqlContentEntityStorage implements APIGatewayStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(APIGatewayInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {api_gateway_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {api_gateway_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

}
