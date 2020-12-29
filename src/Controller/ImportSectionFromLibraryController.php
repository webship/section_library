<?php

namespace Drupal\section_library\Controller;

use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\layout_builder\SectionStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\section_library\Entity\SectionLibraryTemplate;
use Drupal\layout_builder\Controller\LayoutRebuildTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;

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
   * AddSectionController constructor.
   *
   * @param \Drupal\layout_builder\LayoutTempstoreRepositoryInterface $layout_tempstore_repository
   *   The layout tempstore repository.
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository) {
    $this->layoutTempstoreRepository = $layout_tempstore_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('layout_builder.tempstore_repository')
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
    $section_library_template = SectionLibraryTemplate::load($section_library_id);
    $sections = $section_library_template->get('layout_section')->getValue();
    if ($sections) {
      foreach ($sections as $section) {
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
