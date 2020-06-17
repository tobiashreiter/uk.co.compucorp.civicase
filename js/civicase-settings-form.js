(function ($) {
  $(document).on('crmLoad', function () {
    var $allowWebformCheckBoxes = $('.civicase__settings__allow-webform');
    var $webformUrlFields = $('.civicase__settings__webform-url');
    var $showWebforms = $('.civicase__settings__show-webform');
    var $webformButtonLabel = $('.civicase__settings__webform-button-label');

    (function init () {
      showHideWebformUrlFields();
      showWebformsDropdownButtonLabel();

      $allowWebformCheckBoxes.change(showHideRelatedFormUrlField);
      $showWebforms.change(showWebformsDropdownButtonLabel);
    })();

    /**
     * Toggles the visibility of all the webform URL fields.
     */
    function showHideWebformUrlFields () {
      $allowWebformCheckBoxes.filter(':checked')
        .each(showHideRelatedFormUrlField);
    }

    /**
     * Toggles the visibility of webforms button label text box
     */
    function showWebformsDropdownButtonLabel () {
      var isWebformsDropdownVisible = parseInt($showWebforms.filter(':checked').val());

      isWebformsDropdownVisible
        ? $webformButtonLabel.parents('tr').show()
        : $webformButtonLabel.parents('tr').hide();
    }

    /**
     * Toggles the visibility of a webform URL field that is related to the
     * referenced "Allow Webform" field. `$(this)` refers to the "Allow Webform"
     * field.
     */
    function showHideRelatedFormUrlField () {
      var isAllowed = $(this).val() === '1';
      var caseCategoryName = $(this).attr('data-case-category-name');
      var $relatedWebformUrlFieldContainer = $webformUrlFields
        .filter('[data-case-category-name="' + caseCategoryName + '"]')
        .parents('tr');

      isAllowed
        ? $relatedWebformUrlFieldContainer.show()
        : $relatedWebformUrlFieldContainer.hide();
    }
  });
})(CRM.$);
