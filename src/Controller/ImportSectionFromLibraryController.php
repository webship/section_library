<?php

namespace Drupal\section_library\Controller;

use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\layout_builder\SectionStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\section_library\Entity\SectionLibraryTemplate;
use Drupal\layout_builder\Controller\LayoutRebuildTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\SectionComponent;

/**
 * Defines a controller to import a section.
 *
 * @internal
 *   Controller classes are internal.
 */
class ImportSectionFromLibraryController implements ContainerInjectionInterface {

  use AjaxHelperTrait;
  use LayoutRebuildTrait;

  /**
   * The layout tempstore repository.
   *
   * @var \Drupal\layout_builder\LayoutTempstoreRepositoryInterface
   */
  protected $layoutTempstoreRepository;

  /**
   * The UUID generator.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $uuidGenerator;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * AddSectionController constructor.
   *
   * @param \Drupal\layout_builder\LayoutTempstoreRepositoryInterface $layout_tempstore_repository
   *   The layout tempstore repository.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid
   *   The UUID generator.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository, UuidInterface $uuid, EntityTypeManagerInterface $entity_type_manager) {
    $this->layoutTempstoreRepository = $layout_tempstore_repository;
    $this->uuidGenerator = $uuid;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('layout_builder.tempstore_repository'),
      $container->get('uuid'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Provides the UI for choosing a new block.
   *
   * @param int $section_library_id
   *   The entity id.
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The delta of the section to splice.
   *
   * @return array
   *   A render array.
   */
  public function build($section_library_id, SectionStorageInterface $section_storage, $delta) {
    $allowed_types = [
      'block_content',
    ];

    $section_library_template = SectionLibraryTemplate::load($section_library_id);
    $sections = $section_library_template->get('layout_section')->getValue();
    if ($sections) {
      $reversed_sections = array_reverse($sections);
      foreach ($reversed_sections as $section) {
        foreach ($section['section']->getComponents() as $uuid => $component) {
          $component_array = $component->toArray();
          $configuration = $component_array['configuration'];

          $new_component = new SectionComponent($this->uuidGenerator->generate(), $component->getRegion(), $configuration);
          if (isset($configuration['block_revision_id'])) {
            $entity_revision_id = $configuration['block_revision_id'];
            $entity = $this->entityTypeManager->getStorage('block_content')->loadRevision($entity_revision_id);
            $duplicate_entity = $entity->createDuplicate();
            $duplicate_entity->save();
            $entity_fields = $duplicate_entity->getFields();
            foreach ($entity_fields as $field_key => $entity_field) {
              $value = $entity_field->getValue();
              if ($entity_field->getName() != 'type' && isset($value[0]['target_id'])) {
                $target_entity = $entity_field->getDataDefinition()->getTargetEntityTypeId();
                if (in_array($target_entity, $allowed_types)) {
                  // Create a duplicate entity reference and replace
                  // the current target ids with the new entites.
                  $new_referenced_target_ids = [];
                  foreach ($entity_field->referencedEntities() as $entity_reference) {
                    $new_entity_reference = $entity_reference->createDuplicate();
                    $new_entity_reference->save();
                    $new_referenced_target_ids[] = ['target_id' => $new_entity_reference->id()];
                  }
                  $duplicate_entity->set($field_key, $new_referenced_target_ids);
                  $duplicate_entity->save();
                }
              }
            }
            // Remove old component.
            $section['section']->removeComponent($uuid);
            // Insert the new component.
            $new_revision_id = $duplicate_entity->getRevisionId();
            $configuration['block_revision_id'] = $new_revision_id;
            $new_component->setConfiguration($configuration);

            $section['section']->insertComponent(0, $new_component);
          }
        }
        // Create a new section.
        $section_storage->insertSection($delta, $section['section']);
      }
    }

    $this->layoutTempstoreRepository->set($section_storage);

    if ($this->isAjax()) {
      return $this->rebuildAndClose($section_storage);
    }
    else {
      $url = $section_storage->getLayoutBuilderUrl();
      return new RedirectResponse($url->setAbsolute()->toString());
    }
  }

}
