(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('CaseCollector', CaseCollector);

  /**
   * Case Collector Service
   *
   * @param {Function} isTruthy service to check if value is truthy
   */
  function CaseCollector (isTruthy) {
    var allCaseCollectors = CRM['civicase-base'].caseCollectors;
    var activeCaseCollector = _.chain(allCaseCollectors)
      .filter(function (caseCollector) {
        return isTruthy(caseCollector.is_active);
      })
      .indexBy('value')
      .value();

    this.getAll = getAll;
    this.getLabelsForValues = getLabelsForValues;

    /**
     * Get all Case collectors
     *
     * @param {Array} includeInactive if disabled option values also should be returned
     * @returns {object[]} a list of all the case collectors.
     */
    function getAll (includeInactive) {
      var returnValue = includeInactive ? allCaseCollectors : activeCaseCollector;

      return returnValue;
    }

    /**
     * Returns the labels for the given case collector values.
     *
     * @param {string[]} collectorValues a list of case collector values.
     * @returns {string[]} a list of case collector labels.
     */
    function getLabelsForValues (collectorValues) {
      return _.map(collectorValues, function (collectorValue) {
        var caseCollector = _.find(allCaseCollectors, function (caseCollector) {
          return caseCollector.value === collectorValue;
        });

        return caseCollector.label;
      });
    }
  }
})(angular, CRM.$, CRM._, CRM);
