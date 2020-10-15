<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface APIGatewayStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of APIGateway revision IDs for a specific APIGateway.
   *
   * @param \Drupal\api_publisher\Entity\APIGatewayInterface $entity
   *   The APIGateway entity.
   *
   * @return int[]
   *   APIGateway revision IDs (in ascending order).
   */
  public function revisionIds(APIGatewayInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as APIGateway author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   APIGateway revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

}
