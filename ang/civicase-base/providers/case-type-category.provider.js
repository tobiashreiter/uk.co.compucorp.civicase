(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.provider('CaseTypeCategory', CaseTypeCategoryProvider);

  /**
   * CaseTypeCategory Service Provider
   */
  function CaseTypeCategoryProvider () {
    var allCaseTypeCategories = CRM['civicase-base'].caseTypeCategories;
    var activeCaseTypeCategories = _.chain(allCaseTypeCategories)
      .filter(function (caseTypeCategory) {
        return caseTypeCategory.is_active === '1';
      })
      .indexBy('value')
      .value();

    this.$get = $get;
    this.getAll = getAll;
    this.findByName = findByName;

    /**
     * Returns the case the category service.
     *
     * @returns {object} the case type service.
     */
    function $get () {
      return {
        getAll: getAll,
        findByName: findByName
      };
    }

    /**
     * Returns all case type categories.
     *
     * @param {Array} includeInactive if disabled option values also should be returned
     * @returns {object[]} all the case type categories.
     */
    function getAll (includeInactive) {
      var returnValue = includeInactive ? allCaseTypeCategories : activeCaseTypeCategories;

      return returnValue;
    }

    /**
     * Find case type category by name
     *
     * @param {string} caseTypeCategoryName case type category name
     * @returns {object} case type category object
     */
    function findByName (caseTypeCategoryName) {
      return _.find(allCaseTypeCategories, function (category) {
        return category.name === caseTypeCategoryName;
      });
    }
  }
})(angular, CRM.$, CRM._, CRM);
