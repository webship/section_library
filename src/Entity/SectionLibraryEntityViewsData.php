<?php

namespace Drupal\section_library\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Section library entity entities.
 */
class SectionLibraryEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
