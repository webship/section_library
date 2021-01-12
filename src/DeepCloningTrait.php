<?php

namespace Drupal\section_library;

use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\Section;

/**
 * A Trait for deep cloning methods for section inline blocks.
 */
trait DeepCloningTrait {

  /**
   * The allowed types for deep cloning.
   *
   * @return array
   *   Array of enitiy types ids.
   */
  protected function getAllowedTypes() {
    return [
      'block_content',
    ];
  }

  /**
   * Deep clone template sections.
   *
   * @param array $sections
   *   Array of sections.
   *
   * @return array
   *   Array of deep cloned sections.
   */
  protected function deepCloneSections(array $sections) {
    $deep_cloned_sections = [];
    foreach ($sections as $section) {
      $deep_cloned_sections[] = $this->deepCloneSection($section);
    }
    return $deep_cloned_sections;
  }

  /**
   * Deep clone section.
   *
   * @param object $section
   *   The section object.
   *
   * @return object
   *   The new section object.
   */
  protected function deepCloneSection($section) {
    $section_array = $section->toArray();

    // Clone section.
    $cloned_section = new Section(
      $section->getLayoutId(),
      $section->getLayoutSettings(),
      $section->getComponents(),
      $section_array['third_party_settings'],
    );

    // Replace section components with new instances.
    $deep_cloned_section = $this->cloneAndReplaceSectionComponents($cloned_section);

    return $deep_cloned_section;
  }

  /**
   * Clone and replace the section compnenets.
   *
   * @param object $section
   *   The section object.
   *
   * @return object
   *   The modefied section object.
   */
  protected function cloneAndReplaceSectionComponents($section) {
    foreach ($section->getComponents() as $uuid => $component) {
      $component_array = $component->toArray();
      $configuration = $component_array['configuration'];
      $additional = $component_array['additional'];
      // Create a new component.
      $new_component = new SectionComponent($this->uuidGenerator->generate(), $component->getRegion(), $configuration, $additional);
      // If the component is a single-use do a deep cloning of it.
      if (isset($configuration['block_revision_id'])) {
        $entity_revision_id = $configuration['block_revision_id'];
        $entity = $this->entityTypeManager->getStorage('block_content')->loadRevision($entity_revision_id);
        // Create a duplicate entity for the first level.
        $duplicate_entity = $entity->createDuplicate();
        $duplicate_entity->save();
        // Duplicate referenced entities of allowed types.
        $this->cloneReferencedEntities($duplicate_entity);
        // Update the configuration with the new entity block_revision_id.
        $new_revision_id = $duplicate_entity->getRevisionId();
        $configuration['block_revision_id'] = $new_revision_id;
      }

      $new_component->setWeight($component->getWeight());
      $new_component->setConfiguration($configuration);

      // Insert the new component after the old one.
      $section->insertAfterComponent($uuid, $new_component);

      // Then remove old component.
      $section->removeComponent($uuid);
    }

    return $section;
  }

  /**
   * Clone entity refernced entites.
   *
   * @param object $entity
   *   The entity we like duplicate its ReferencedEntities.
   */
  protected function cloneReferencedEntities($entity) {
    $entity_fields = $entity->getFields();
    foreach ($entity_fields as $field_key => $entity_field) {
      $value = $entity_field->getValue();
      if ($entity_field->getName() != 'type' && isset($value[0]['target_id'])) {
        $target_entity = $entity_field->getDataDefinition()->getTargetEntityTypeId();

        if (in_array($target_entity, $this->getAllowedTypes())) {
          // Create a duplicate entity reference and replace
          // the current target ids with the new entites.
          $new_referenced_target_ids = [];
          foreach ($entity_field->referencedEntities() as $entity_reference) {
            $new_entity_reference = $entity_reference->createDuplicate();
            $new_entity_reference->save();
            $new_referenced_target_ids[] = ['target_id' => $new_entity_reference->id()];
            // Recursive call.
            $this->cloneReferencedEntities($new_entity_reference);
          }
          $entity->set($field_key, $new_referenced_target_ids);
          $entity->save();
        }
      }
    }
  }

}
