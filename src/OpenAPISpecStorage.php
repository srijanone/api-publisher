<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\api_publisher\Entity\OpenAPISpecInterface;

/**
 * Defines the storage handler class for OpenAPI Specification entities.
 *
 * This extends the base storage class, adding required special handling for
 * OpenAPI Specification entities.
 *
 * @ingroup api_publisher
 */
class OpenAPISpecStorage extends SqlContentEntityStorage implements OpenAPISpecStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(OpenAPISpecInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {open_api_spec_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {open_api_spec_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

}
