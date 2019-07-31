(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseCaseActions', function ($window, $injector, dialogService, PrintMergeCaseAction) {
    return {
      restrict: 'A',
      templateUrl: '~/civicase/case/actions/directives/case-actions.directive.html',
      scope: {
        cases: '=civicaseCaseActions',
        refresh: '=refreshCallback',
        popupParams: '='
      },
      link: civicaseCaseActionsLink
    };

    /**
     * Angular JS's link function for civicaseCaseActions Directive
     *
     * @param {Object} $scope
     * @param {Object} element
     * @param {Object} attributes
     */
    function civicaseCaseActionsLink ($scope, element, attributes) {
      var ts = CRM.ts('civicase');
      var isBulkMode = attributes.isBulkMode;

      $scope.hasSubMenu = function (action) {
        return (action.items && action.items.length);
      };

      $scope.isActionEnabled = function (action) {
        return (!action.number || $scope.cases.length === +action.number);
      };

      $scope.isActionAllowed = function (action) {
        var isActionAllowed = true;
        var isLockCaseAction = _.startsWith(action.action, 'lockCases');
        var isCaseLockAllowed = CRM.civicase.allowCaseLocks;
        var caseActionService = getCaseActionService(action.action);

        if (caseActionService && caseActionService.isActionAllowed) {
          isActionAllowed = caseActionService.isActionAllowed(action, $scope.cases);
        }

        return isActionAllowed && ((isLockCaseAction && isCaseLockAllowed) ||
          (!isLockCaseAction && (!action.number || ((isBulkMode && action.number > 1) || (!isBulkMode && action.number === 1)))));
      };

      function getCaseActionService (action) {
        try {
          return $injector.get(action + 'CaseAction');
        } catch (e) {
          return false;
        }
      }

      // Perform bulk actions
      $scope.doAction = function (action) {
        var caseActionService = getCaseActionService(action.action);

        if (!$scope.isActionEnabled(action) || !caseActionService) {
          return;
        }

        var result = caseActionService.doAction($scope.cases, action, $scope.refresh);
        // Open popup if callback returns a path & query
        if (result) {
          // Add refresh data
          if ($scope.popupParams) {
            result.query.civicase_reload = $scope.popupParams();
          }

          // Mimic the behavior of CRM.popup()
          var formData = false;
          var dialog = CRM.loadForm(CRM.url(result.path, result.query))
            // Listen for success events and buffer them so we only trigger once
            .on('crmFormSuccess crmPopupFormSuccess', function (e, data) {
              formData = data;
            })
            .on('dialogclose.crmPopup', function (e, data) {
              if (formData) {
                element.trigger('crmPopupFormSuccess', [dialog, formData]);
              }

              element.trigger('crmPopupClose', [dialog, data]);
            });
        }
      };

      $scope.$watchCollection('cases', function (cases) {
        // Special actions when viewing deleted cases
        if (cases.length && cases[0].is_deleted) {
          $scope.caseActions = [
            { action: 'DeleteCases', type: 'delete', title: ts('Delete Permanently') },
            { action: 'DeleteCases', type: 'restore', title: ts('Restore from Trash') }
          ];
        } else {
          $scope.caseActions = _.cloneDeep(CRM.civicase.caseActions);

          if (!isBulkMode) {
            _.remove($scope.caseActions, { action: 'changeStatus(cases)' });
          }
        }

        _.each($scope.caseActions, function (action) {
          var caseActionService = getCaseActionService(action.action);

          if (caseActionService && caseActionService.refreshData) {
            caseActionService.refreshData($scope.cases);
          }
        });
      });
    }
  });
})(angular, CRM.$, CRM._);
