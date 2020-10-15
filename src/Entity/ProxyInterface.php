<?php

namespace Drupal\api_publisher\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Proxy entities.
 *
 * @ingroup api_publisher
 */
interface ProxyInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Proxy name.
   *
   * @return string
   *   Name of the Proxy.
   */
  public function getName();

  /**
   * Sets the Proxy name.
   *
   * @param string $name
   *   The Proxy name.
   *
   * @return \Drupal\api_publisher\Entity\ProxyInterface
   *   The called Proxy entity.
   */
  public function setName($name);

  /**
   * Gets the Proxy creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Proxy.
   */
  public function getCreatedTime();

  /**
   * Sets the Proxy creation timestamp.
   *
   * @param int $timestamp
   *   The Proxy creation timestamp.
   *
   * @return \Drupal\api_publisher\Entity\ProxyInterface
   *   The called Proxy entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Proxy revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Proxy revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\api_publisher\Entity\ProxyInterface
   *   The called Proxy entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Proxy revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Proxy revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\api_publisher\Entity\ProxyInterface
   *   The called Proxy entity.
   */
  public function setRevisionUserId($uid);

}
