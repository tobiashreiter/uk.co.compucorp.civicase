(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseCaseDetailsHeader', function () {
    return {
      replace: true,
      restrict: 'E',
      templateUrl: '~/civicase/case/details/directives/case-details-header.directive.html',
      controller: 'civicaseCaseDetailsHeaderController'
    };
  });

  module.controller('civicaseCaseDetailsHeaderController', civicaseCaseDetailsHeaderController);

  /**
   *
   * @param {*} $scope scope object of the controller
   * @param {*} CaseActions case action service
   * @param {*} WebformsCaseAction webform case action service
   * @param {*} GoToWebformCaseAction go to webform case action service
   */
  function civicaseCaseDetailsHeaderController ($scope, CaseActions, WebformsCaseAction, GoToWebformCaseAction) {
    $scope.webformsAction = CaseActions.findByActionName('Webforms');
    $scope.isGoToWebformAllowed = isGoToWebformAllowed;
    $scope.openWebform = openWebform;

    (function init () {
      $scope.$watch('item', itemWatcher);
    })();

    /**
     * @param {object} action action object
     */
    function openWebform (action) {
      GoToWebformCaseAction.doAction([$scope.item], action);
    }

    /**
     * @param {object} action action object
     * @returns {object} is go to webform action allowed for the sent action
     */
    function isGoToWebformAllowed (action) {
      return GoToWebformCaseAction.isActionAllowed(action, [$scope.item]);
    }

    /**
     * Watcher function for the "$scope.item"
     * It checks if the Case Webform Dropdown is visible
     */
    function itemWatcher () {
      $scope.isCaseWebformDropdownVisible = WebformsCaseAction.isActionAllowed(
        $scope.webformsAction, [$scope.item], { mode: 'case-details' });
    }
  }
})(angular, CRM.$, CRM._);
