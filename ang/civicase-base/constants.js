(function (angular, configuration) {
  var module = angular.module('civicase-base');

  module
    .constant('allowCaseLocks', configuration.allowCaseLocks)
    .constant('allowMultipleCaseClients', configuration.allowMultipleCaseClients)
    .constant('currentCaseCategory', configuration.currentCaseCategory)
    .constant('newCaseWebformClient', configuration.newCaseWebformClient)
    .constant('newCaseWebformUrl', configuration.newCaseWebformUrl);
})(angular, CRM['civicase-base']);
