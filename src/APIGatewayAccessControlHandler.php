<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the APIGateway entity.
 *
 * @see \Drupal\api_publisher\Entity\APIGateway.
 */
class APIGatewayAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\api_publisher\Entity\APIGatewayInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished apigateway entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published apigateway entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit apigateway entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete apigateway entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add apigateway entities');
  }


}
