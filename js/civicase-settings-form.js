(function ($) {
  $(document).on('crmLoad', function () {
    var $elementsThatToggleVisibility = $('[data-toggles-visibility-for]');

    (function init () {
      toggleVisibility();

      $elementsThatToggleVisibility.change(toggleVisibility);
    })();

    /**
     * Toggles the visibility of civicase settings fields based on value.
     */
    function toggleVisibility () {
      $elementsThatToggleVisibility.filter(':checked')
        .each(function () {
          var $element = $(this);
          var isAllowed = $element.val() === '1';
          var $elementToToggle =
            $('.' + $element.data('toggles-visibility-for')).parents('tr');

          isAllowed ? $elementToToggle.show() : $elementToToggle.hide();
        });
    }
  });
})(CRM.$);
