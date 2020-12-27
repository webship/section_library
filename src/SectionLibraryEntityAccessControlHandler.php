<?php

namespace Drupal\section_library;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Section library entity entity.
 *
 * @see \Drupal\section_library\Entity\SectionLibraryEntity.
 */
class SectionLibraryEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\section_library\Entity\SectionLibraryEntityInterface $entity */

    switch ($operation) {

      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view published section library entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit section library entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete section library entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add section library entity entities');
  }

}
