<?php

namespace Drupal\api_publisher\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining OpenAPI Specification entities.
 *
 * @ingroup api_publisher
 */
interface OpenAPISpecInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the OpenAPI Specification name.
   *
   * @return string
   *   Name of the OpenAPI Specification.
   */
  public function getName();

  /**
   * Sets the OpenAPI Specification name.
   *
   * @param string $name
   *   The OpenAPI Specification name.
   *
   * @return \Drupal\api_publisher\Entity\OpenAPISpecInterface
   *   The called OpenAPI Specification entity.
   */
  public function setName($name);

  /**
   * Gets the OpenAPI Specification creation timestamp.
   *
   * @return int
   *   Creation timestamp of the OpenAPI Specification.
   */
  public function getCreatedTime();

  /**
   * Sets the OpenAPI Specification creation timestamp.
   *
   * @param int $timestamp
   *   The OpenAPI Specification creation timestamp.
   *
   * @return \Drupal\api_publisher\Entity\OpenAPISpecInterface
   *   The called OpenAPI Specification entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the OpenAPI Specification revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the OpenAPI Specification revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\api_publisher\Entity\OpenAPISpecInterface
   *   The called OpenAPI Specification entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the OpenAPI Specification revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the OpenAPI Specification revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\api_publisher\Entity\OpenAPISpecInterface
   *   The called OpenAPI Specification entity.
   */
  public function setRevisionUserId($uid);

}
