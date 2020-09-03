(function ($) {
  $(document).on('crmLoad', function () {
    var $radioButtonsThatToggleVisibility = $('[data-toggles-visibility-for]');
    var $multiCaseClient = $('#civicaseAllowMultipleClients');

    (function init () {
      toggleVisibilityOnRadioButtonChange();
      toggleVisibilityForSingleCaseRole();

      $radioButtonsThatToggleVisibility.change(toggleVisibilityOnRadioButtonChange);
      if ($multiCaseClient.length) {
        $multiCaseClient.change(toggleVisibilityForSingleCaseRole);
      }
    })();

    /**
     * Toggles the visibility of civicase settings fields based on radio button values.
     */
    function toggleVisibilityOnRadioButtonChange () {
      $radioButtonsThatToggleVisibility.filter(':checked')
        .each(function () {
          var $element = $(this);
          var isAllowed = $element.val() === '1';
          var $elementToToggle =
            $('.' + $element.data('toggles-visibility-for')).parents('tr');

          isAllowed ? $elementToToggle.show() : $elementToToggle.hide();
        });
    }

    /**
     * Toggles the visibility of single case role.
     */
    function toggleVisibilityForSingleCaseRole () {
      var $defaultMultiCaseClientValue = $('.CRM_Admin_Form_Setting_Case').attr('defaultMultipleCaseClient') ?
        $('.CRM_Admin_Form_Setting_Case').attr('defaultMultipleCaseClient') :
        0;
      $multiCaseClient.val() == 0 ||
      ($multiCaseClient.val().toLowerCase() == 'default' && $defaultMultiCaseClientValue == 0) ?
        showSingleCaseRole() :
        hideSingleCaseRole();
    }

    /**
     * Shows single case role.
     */
    function showSingleCaseRole () {
      if ($('.crm-mail-form-block-civicaseSingleCaseRolePerType').length) {
        $('.crm-mail-form-block-civicaseSingleCaseRolePerType').show();
      }
    }

    /**
     * Hides single case role.
     */
    function hideSingleCaseRole () {
      if ($('.crm-mail-form-block-civicaseSingleCaseRolePerType').length) {
        $('.crm-mail-form-block-civicaseSingleCaseRolePerType').hide();
      }
      if ($('#civicaseSingleCaseRolePerType_civicaseSingleCaseRolePerType').length) {
        $('#civicaseSingleCaseRolePerType_civicaseSingleCaseRolePerType').prop("checked", false);
      }
    }

  });
})(CRM.$);
