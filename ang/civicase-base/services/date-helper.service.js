(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('DateHelper', DateHelper);

  /**
   * Date Helper service.
   */
  function DateHelper () {
    /**
     * Formats Date in sent format
     * Default format is (DD/MM/YYYY)
     *
     * @param {string} date ISO string
     * @param {string} format moment's date format
     * @returns {string} the formatted date
     */
    this.formatDate = function (date, format) {
      format = format || 'DD/MM/YYYY';

      if (typeof CRM.config.locale != 'undefined') {
        var locale = CRM.config.locale.substr(0,2);
        moment.locale(locale);
      }

      return moment(date).format(format);
    };
  }
})(angular, CRM.$, CRM._, CRM);
