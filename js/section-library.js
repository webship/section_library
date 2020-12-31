/**
 * @file
 * Behaviors Section Library general scripts.
 */

(function ($, _, Drupal, drupalSettings) {
  "use strict";

  var layoutBuilderSectionLibraryFiltered = false;

  Drupal.behaviors.sctionLibrary = {
    attach: function (context) {
      var $filterLinks = $('.js-layout-builder-section-library-link');

      var filterSectionLibraryList = function filterSectionLibraryList(e) {
        var query = $(e.target).val().toLowerCase();

        var toggleSectionLibraryEntry = function toggleSectionLibraryEntry(index, link) {
          var $link = $(link);
          var textMatch = $link.text().toLowerCase().indexOf(query) !== -1;
          $link.parent().toggle(textMatch);
        };

        if (query.length >= 2) {
          $filterLinks.each(toggleSectionLibraryEntry);
          layoutBuilderSectionLibraryFiltered = true;
        } else if (layoutBuilderSectionLibraryFiltered) {
          layoutBuilderSectionLibraryFiltered = false;
          $filterLinks.parent().show();
          Drupal.announce(Drupal.t('All available sections are listed.'));
        }
      };

      $('input.js-layout-builder-section-library-filter', context).once('js-layout-builder-section-library-filter').on('keyup', Drupal.debounce(filterSectionLibraryList, 200));
    }
  };

})(window.jQuery, window._, window.Drupal, window.drupalSettings);
