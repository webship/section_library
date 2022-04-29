<?php

namespace Drupal\Tests\section_library\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test install of module.
 *
 * @group section_library
 */
class SectionLibraryTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'section_library',
    'layout_builder',
    'options',
    'file',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Set up the test here.
  }

  /**
   * Test callback.
   */
  public function testInstall(): void {
    $admin_user = $this->drupalCreateUser([
      'administer site configuration',
      'access administration pages',
      'administer section library template entities',
      'view section library templates',
      'add section library templates',
    ]);
    $this->drupalLogin($admin_user);
    $this->drupalGet('/admin/content/section-library');
    $this->assertSession()->pageTextContains('There are no section library template entities yet.');
  }

}
