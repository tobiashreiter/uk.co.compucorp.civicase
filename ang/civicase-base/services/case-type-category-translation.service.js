(function (_, angular) {
  var module = angular.module('civicase-base');

  module.service('CaseTypeCategoryTranslationService', function ($rootScope) {
    var civicaseTranslationDomainName = 'strings::uk.co.compucorp.civicase';

    this.restoreTranslation = restoreTranslation;
    this.storeTranslation = storeTranslation;

    (function init () {
      createTranslationStore();
    })();

    /**
     * Creates a map used to store translations for a particular case type category.
     */
    function createTranslationStore () {
      if (!$rootScope.caseTypeCategoryTranslations) {
        $rootScope.caseTypeCategoryTranslations = {};
      }
    }

    /**
     * Restores the translation for the given case type category.
     *
     * @param {number} caseTypeCategoryId the case type category id.
     */
    function restoreTranslation (caseTypeCategoryId) {
      CRM[civicaseTranslationDomainName] =
        $rootScope.caseTypeCategoryTranslations[caseTypeCategoryId];
    }

    /**
     * Stores the translation for the given case type category.
     *
     * @param {number} caseTypeCategoryId the case type category id.
     */
    function storeTranslation (caseTypeCategoryId) {
      $rootScope.caseTypeCategoryTranslations[caseTypeCategoryId] =
        _.clone(CRM[civicaseTranslationDomainName]);
    }
  });
})(CRM._, angular);
