(function (angular, $, _) {
  var module = angular.module('civicase');

  module.directive('civicaseCaseActions', function ($window, dialogService, PrintMergeCaseAction) {
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

        if ($scope['isActionAllowed' + action.action]) {
          isActionAllowed = $scope['isActionAllowed' + action.action](action, $scope.cases);
        }

        return isActionAllowed && ((isLockCaseAction && isCaseLockAllowed) ||
          (!isLockCaseAction && (!action.number || ((isBulkMode && action.number > 1) || (!isBulkMode && action.number === 1)))));
      };

      // Perform bulk actions
      $scope.doAction = function (action) {
        if (!$scope.isActionEnabled(action)) {
          return;
        }

        var result = $scope[action.action](action);
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

      $scope.editTags = function (action) {
        var item = $scope.cases[0];
        var keys = ['tags'];
        var model = {
          tags: []
        };

        _.each(CRM.civicase.tagsets, function (tagset) {
          model[tagset.id] = [];
          keys.push(tagset.id);
        });

        // Sort case tags into sets
        _.each(item.tag_id, function (tag, id) {
          if (!tag['tag_id.parent_id'] || !model[tag['tag_id.parent_id']]) {
            model.tags.push(id);
          } else {
            model[tag['tag_id.parent_id']].push(id);
          }
        });

        model.tagsets = CRM.civicase.tagsets;
        model.colorTags = CRM.civicase.tags;
        model.ts = ts;

        dialogService.open('EditTags', '~/civicase/case/actions/directives/edit-tags.html', model, {
          autoOpen: false,
          height: 'auto',
          width: '40%',
          title: action.title,
          buttons: [{
            text: ts('Save'),
            icons: { primary: 'fa-check' },
            click: editTagModalClickEvent
          }]
        });

        /**
         * Handles the click event for the Edit Tag Modal's Click Event
         */
        function editTagModalClickEvent () {
          var calls = [];
          var values = [];

          function tagParams (tagIds) {
            var params = { entity_id: item.id, entity_table: 'civicrm_case' };

            _.each(tagIds, function (id, i) {
              params['tag_id_' + i] = id;
            });

            return params;
          }

          _.each(keys, function (key) {
            _.each(model[key], function (id) {
              values.push(id);
            });
          });

          var toRemove = _.difference(_.keys(item.tag_id), values);
          var toAdd = _.difference(values, _.keys(item.tag_id));

          if (toRemove.length) {
            calls.push(['EntityTag', 'delete', tagParams(toRemove)]);
          }

          if (toAdd.length) {
            calls.push(['EntityTag', 'create', tagParams(toAdd)]);
          }

          if (calls.length) {
            calls.push(['Activity', 'create', {
              case_id: item.id,
              status_id: 'Completed',
              activity_type_id: 'Change Case Tags'
            }]);
            $scope.refresh(calls);
          }

          $(this).dialog('close');
        }
      };

      $scope.$watchCollection('cases', function (cases) {
        // Special actions when viewing deleted cases
        if (cases.length && cases[0].is_deleted) {
          $scope.caseActions = [
            { action: 'deleteCases(cases, "delete")', title: ts('Delete Permanently') },
            { action: 'deleteCases(cases, "restore")', title: ts('Restore from Trash') }
          ];
        } else {
          $scope.caseActions = _.cloneDeep(CRM.civicase.caseActions);

          if (!isBulkMode) {
            _.remove($scope.caseActions, { action: 'changeStatus(cases)' });
          }
        }
      });

      /**
       * Aligns the dialog box center to the screen
       *
       * @param {jQuery} dialog box to be aligned center
       */
      function alignDialogBoxCenter (dialog) {
        if (dialog && dialog.data('uiDialog')) {
          dialog.parent().position({ 'my': 'center', 'at': 'center', 'of': window });
        }
      }
    }
  });
})(angular, CRM.$, CRM._);
