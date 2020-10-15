<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ProxyStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Proxy revision IDs for a specific Proxy.
   *
   * @param \Drupal\api_publisher\Entity\ProxyInterface $entity
   *   The Proxy entity.
   *
   * @return int[]
   *   Proxy revision IDs (in ascending order).
   */
  public function revisionIds(ProxyInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Proxy author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Proxy revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

}
