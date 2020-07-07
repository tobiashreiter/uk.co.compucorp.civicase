(function (angular, configuration) {
  var module = angular.module('civicase-base');

  module
    .constant('allowCaseLocks', configuration.allowCaseLocks)
    .constant('allowLinkedCasesTab', configuration.allowLinkedCasesTab)
    .constant('allowMultipleCaseClients', configuration.allowMultipleCaseClients)
    .constant('currentCaseCategory', configuration.currentCaseCategory)
    .constant('showFullContactNameOnActivityFeed', configuration.showFullContactNameOnActivityFeed)
    .constant('webformsList', {
      isVisible: configuration.showWebformsListSeparately,
      buttonLabel: configuration.webformsDropdownButtonLabel
    });
})(angular, CRM['civicase-base']);
