<?php

namespace Drupal\webform\Tests;

/**
 * Tests for webform libraries.
 *
 * @group Webform
 */
class WebformLibrariesTest extends WebformTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['webform_ui'];

  /**
   * Webforms to load.
   *
   * @var array
   */
  protected static $testWebforms = ['test_libraries_optional'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Create users.
    $this->createUsers();
  }

  /**
   * Tests webform libraries.
   */
  public function testLibraries() {
    $optional_properties = [
      'icheck' => 'properties[icheck]',
      'input_mask' => 'properties[input_mask][select]',
      'international_telephone' => 'properties[international]',
      'international_telephone_composite' => 'properties[phone__international]',
      'word_counter' => 'properties[counter_type]',
      'select2' => 'properties[select2]',
    ];

    $this->drupalLogin($this->rootUser);

    // Enable jquery.chosen and jquery.icheck.
    $edit = [
      'excluded_libraries[jquery.chosen]' => TRUE,
      'excluded_libraries[jquery.icheck]' => TRUE,
    ];
    $this->drupalPostForm('admin/structure/webform/config/libraries', $edit, t('Save configuration'));

    // Check optional libraries are included.
    $this->drupalGet('webform/test_libraries_optional');
    $this->assertRaw('/select2.min.js');
    $this->assertRaw('/chosen.jquery.js');
    $this->assertRaw('/textcounter.min.js');
    $this->assertRaw('/intlTelInput.min.js');
    $this->assertRaw('/jquery.inputmask.bundle.min.js');
    $this->assertRaw('/icheck.js');
    $this->assertRaw('/codemirror.js');
    $this->assertRaw('/jquery.timepicker.min.js');

    // Check optional libraries are properties accessible (#access = TRUE).
    foreach ($optional_properties as $element_name => $input_name) {
      $this->drupalGet("/admin/structure/webform/manage/test_libraries_optional/element/$element_name/edit");
      $this->assertFieldByName($input_name);
    }

    // Exclude optional libraries.
    $edit = [
      'excluded_libraries[ckeditor.fakeobjects]' => FALSE,
      'excluded_libraries[ckeditor.image]' => FALSE,
      'excluded_libraries[ckeditor.link]' => FALSE,
      'excluded_libraries[codemirror]' => FALSE,
      'excluded_libraries[jquery.icheck]' => FALSE,
      'excluded_libraries[jquery.inputmask]' => FALSE,
      'excluded_libraries[jquery.intl-tel-input]' => FALSE,
      'excluded_libraries[jquery.select2]' => FALSE,
      'excluded_libraries[jquery.chosen]' => FALSE,
      'excluded_libraries[jquery.timepicker]' => FALSE,
      'excluded_libraries[jquery.textcounter]' => FALSE,
    ];
    $this->drupalPostForm('admin/structure/webform/config/libraries', $edit, t('Save configuration'));

    // Check optional libraries are excluded.
    $this->drupalGet('webform/test_libraries_optional');
    $this->assertNoRaw('/select2.min.js');
    $this->assertNoRaw('/chosen.jquery.js');
    $this->assertNoRaw('/textcounter.min.js');
    $this->assertNoRaw('/intlTelInput.min.js');
    $this->assertNoRaw('/jquery.inputmask.bundle.min.js');
    $this->assertNoRaw('/icheck.js');
    $this->assertNoRaw('/codemirror.js');
    $this->assertNoRaw('/jquery.timepicker.min.js');

    // Check optional libraries are properties hidden (#access = FALSE).
    foreach ($optional_properties as $element_name => $input_name) {
      $this->drupalGet("admin/structure/webform/manage/test_libraries_optional/element/$element_name/edit");
      $this->assertNoFieldByName($input_name);
    }

    // Check that status report excludes optional libraries.
    $this->drupalGet('admin/reports/status');
    $this->assertText('The CKEditor: Fakeobjects library is excluded.');
    $this->assertText('The CKEditor: Image library is excluded.');
    $this->assertText('The CKEditor: Link library is excluded.');
    $this->assertText('The Code Mirror library is excluded.');
    $this->assertText('The jQuery: iCheck library is excluded.');
    $this->assertText('The jQuery: Input Mask library is excluded.');
    $this->assertText('The jQuery: Select2 library is excluded.');
    $this->assertText('The jQuery: Chosen library is excluded.');
    $this->assertText('The jQuery: Timepicker library is excluded.');
    $this->assertText('The jQuery: Text Counter library is excluded.');

    // Issue #2934542: Fix broken Webform.Drupal\webform\Tests\WebformLibrariesTest
    // @see https://www.drupal.org/project/webform/issues/2934542
    /*
    // Exclude element types that require libraries.
    $edit = [
      'excluded_elements[webform_image_select]' => FALSE,
      'excluded_elements[webform_location_geocomplete]' => FALSE,
      'excluded_elements[webform_rating]' => FALSE,
      'excluded_elements[webform_signature]' => FALSE,
      'excluded_elements[webform_toggle]' => FALSE,
      'excluded_elements[webform_toggles]' => FALSE,
    ];
    $this->drupalPostForm('admin/structure/webform/config/elements', $edit, t('Save configuration'));

    // Check that status report excludes libraries required by element types.
    $this->drupalGet('admin/reports/status');
    $this->assertText('The jQuery: Geocoding and Places Autocomplete Plugin library is excluded because required element types (webform_location_geocomplete) are excluded.');
    $this->assertText('The jQuery: Image Picker library is excluded because required element types (webform_image_select) are excluded.');
    $this->assertText('The jQuery: RateIt library is excluded because required element types (webform_rating) are excluded.');
    $this->assertText('The jQuery: Toggles library is excluded because required element types (webform_toggle; webform_toggles) are excluded.');
    $this->assertText('The Signature Pad library is excluded because required element types (webform_signature) are excluded.');
    */
  }

}
