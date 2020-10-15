<?php

namespace Drupal\api_publisher;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface OpenAPISpecStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of OpenAPI Specification revision IDs for a specific OpenAPI Specification.
   *
   * @param \Drupal\api_publisher\Entity\OpenAPISpecInterface $entity
   *   The OpenAPI Specification entity.
   *
   * @return int[]
   *   OpenAPI Specification revision IDs (in ascending order).
   */
  public function revisionIds(OpenAPISpecInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as OpenAPI Specification author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   OpenAPI Specification revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

}
