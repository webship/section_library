<?php

namespace Drupal\section_library\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Section library entity entities.
 *
 * @ingroup section_library
 */
interface SectionLibraryEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Section library entity name.
   *
   * @return string
   *   Name of the Section library entity.
   */
  public function getName();

  /**
   * Sets the Section library entity name.
   *
   * @param string $name
   *   The Section library entity name.
   *
   * @return \Drupal\section_library\Entity\SectionLibraryEntityInterface
   *   The called Section library entity entity.
   */
  public function setName($name);

  /**
   * Gets the Section library entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Section library entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Section library entity creation timestamp.
   *
   * @param int $timestamp
   *   The Section library entity creation timestamp.
   *
   * @return \Drupal\section_library\Entity\SectionLibraryEntityInterface
   *   The called Section library entity entity.
   */
  public function setCreatedTime($timestamp);

}
