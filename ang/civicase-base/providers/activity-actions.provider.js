(function (angular, $, _) {
  var module = angular.module('civicase-base');

  module.provider('ActivityActions', function () {
    var actions = [{
      label: 'View in Feed',
      icon: 'pageview',
      serviceName: 'ViewInFeedActivityAction'
    }, {
      label: 'Edit',
      icon: 'edit',
      serviceName: 'EditActivityAction'
    }, {
      label: 'Print Report',
      icon: 'print',
      serviceName: 'PrintReportActivityAction'
    }, {
      label: 'Move to Case',
      icon: 'next_week',
      serviceName: 'MoveCopyActivityAction',
      operation: 'move'
    }, {
      label: 'Copy to Case',
      icon: 'filter_none',
      serviceName: 'MoveCopyActivityAction',
      operation: 'copy'
    }, {
      label: 'Tag - add to activities',
      icon: 'add_circle',
      serviceName: 'TagsActivityAction',
      operation: 'add'
    }, {
      label: 'Tag - remove from activities',
      icon: 'remove_circle',
      serviceName: 'TagsActivityAction',
      operation: 'remove'
    }, {
      label: 'Download All',
      icon: 'file_download',
      serviceName: 'DownloadAllActivityAction'
    }, {
      showDividerBeforeThisAction: true,
      label: 'Delete',
      icon: 'delete',
      serviceName: 'DeleteActivityAction'
    }];

    this.$get = function () {
      return actions;
    };
  });
})(angular, CRM.$, CRM._);
