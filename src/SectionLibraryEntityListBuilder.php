<?php

namespace Drupal\section_library;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Section library entity entities.
 *
 * @ingroup section_library
 */
class SectionLibraryEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Section library entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\section_library\Entity\SectionLibraryEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.section_library_entity.edit_form',
      ['section_library_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
