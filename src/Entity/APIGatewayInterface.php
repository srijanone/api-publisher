<?php

namespace Drupal\api_publisher\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining APIGateway entities.
 *
 * @ingroup api_publisher
 */
interface APIGatewayInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the APIGateway name.
   *
   * @return string
   *   Name of the APIGateway.
   */
  public function getName();

  /**
   * Sets the APIGateway name.
   *
   * @param string $name
   *   The APIGateway name.
   *
   * @return \Drupal\api_publisher\Entity\APIGatewayInterface
   *   The called APIGateway entity.
   */
  public function setName($name);

  /**
   * Gets the APIGateway creation timestamp.
   *
   * @return int
   *   Creation timestamp of the APIGateway.
   */
  public function getCreatedTime();

  /**
   * Sets the APIGateway creation timestamp.
   *
   * @param int $timestamp
   *   The APIGateway creation timestamp.
   *
   * @return \Drupal\api_publisher\Entity\APIGatewayInterface
   *   The called APIGateway entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the APIGateway revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the APIGateway revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\api_publisher\Entity\APIGatewayInterface
   *   The called APIGateway entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the APIGateway revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the APIGateway revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\api_publisher\Entity\APIGatewayInterface
   *   The called APIGateway entity.
   */
  public function setRevisionUserId($uid);

}
