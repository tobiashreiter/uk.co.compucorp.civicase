(function (angular) {
  var module = angular.module('casetype');

  module.config(function (WorkflowListActionItemsProvider) {
    var actionItems = [
      {
        templateUrl: '~/casetype/action-links/directives/workflow-list-edit-action.html',
        weight: 0
      }, {
        templateUrl: '~/casetype/action-links/directives/workflow-list-duplicate-action.html',
        weight: 1
      }
    ];

    WorkflowListActionItemsProvider.addItems(actionItems);
  });
})(angular);
