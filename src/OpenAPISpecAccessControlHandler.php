<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the OpenAPI Specification entity.
 *
 * @see \Drupal\api_publisher\Entity\OpenAPISpec.
 */
class OpenAPISpecAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\api_publisher\Entity\OpenAPISpecInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished openapi specification entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published openapi specification entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit openapi specification entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete openapi specification entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add openapi specification entities');
  }


}
