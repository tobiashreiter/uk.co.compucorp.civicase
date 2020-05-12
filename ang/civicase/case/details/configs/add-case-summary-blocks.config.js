(function (angular) {
  var module = angular.module('civicase');

  module.config(function (CaseDetailsSummaryBlocksProvider) {
    var caseSummaryBlocks = [
      {
        templateUrl: '~/civicase/case/details/summary-tab/case-details-summary-next-milestone.html',
        weight: 0
      }, {
        templateUrl: '~/civicase/case/details/summary-tab/case-details-summary-next-non-milestone-activity.html',
        weight: 0
      }, {
        templateUrl: '~/civicase/case/details/summary-tab/case-details-summary-calendar.html',
        weight: 0
      }
    ];

    CaseDetailsSummaryBlocksProvider.addItems(caseSummaryBlocks);
  });
})(angular);
