<?php

namespace Drupal\section_library\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Section library entity entities.
 *
 * @ingroup section_library
 */
interface SectionLibraryEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

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
