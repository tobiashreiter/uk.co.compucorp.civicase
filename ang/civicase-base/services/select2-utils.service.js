(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('Select2Utils', function () {
    this.getSelect2Value = getSelect2Value;
    this.mapSelectOptions = mapSelectOptions;

    /**
     * Returns Select2 values as arrays. Select2 returns a single selected value
     * as an array, but multiple values as a string separated by comas.
     *
     * @param {Array|string} value the value as provided by Select2.
     * @returns {Array} value
     */
    function getSelect2Value (value) {
      if (value) {
        return _.isArray(value)
          ? value
          : value.split(',');
      } else {
        return [];
      }
    }

    /**
     * Map the option parameter from API
     * to show up correctly on the UI.
     *
     * @param {object} opt object for caseTypes
     * @returns {object} mapped value to be used in UI
     */
    function mapSelectOptions (opt) {
      return {
        id: opt.value || opt.name,
        text: opt.label || opt.title,
        color: opt.color,
        icon: opt.icon
      };
    }
  });
})(angular, CRM.$, CRM._, CRM);
