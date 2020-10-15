<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\api_publisher\Entity\ProxyInterface;

/**
 * Defines the storage handler class for Proxy entities.
 *
 * This extends the base storage class, adding required special handling for
 * Proxy entities.
 *
 * @ingroup api_publisher
 */
class ProxyStorage extends SqlContentEntityStorage implements ProxyStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ProxyInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {proxy_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {proxy_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

}
