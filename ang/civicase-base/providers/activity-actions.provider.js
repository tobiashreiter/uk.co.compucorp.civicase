(function (angular, $, _) {
  var module = angular.module('civicase-base');

  module.provider('ActivityActions', function () {
    var actions = [{
      title: 'Resume Draft',
      icon: 'play_circle_filled',
      name: 'ResumeDraft'
    }, {
      title: 'View in Feed',
      icon: 'pageview',
      name: 'ViewInFeed'
    }, {
      title: 'Edit',
      icon: 'edit',
      name: 'Edit'
    }, {
      title: 'Print Report',
      icon: 'print',
      name: 'PrintReport'
    }, {
      title: 'Move to Case',
      icon: 'next_week',
      name: 'MoveCopy',
      operation: 'move'
    }, {
      title: 'Copy to Case',
      icon: 'filter_none',
      name: 'MoveCopy',
      operation: 'copy'
    }, {
      title: 'Tag - add to activities',
      icon: 'add_circle',
      name: 'Tags',
      operation: 'add'
    }, {
      title: 'Tag - remove from activities',
      icon: 'remove_circle',
      name: 'Tags',
      operation: 'remove'
    }, {
      title: 'Download All',
      icon: 'file_download',
      name: 'DownloadAll'
    }, {
      showDividerBeforeThisAction: true,
      title: 'Delete',
      icon: 'delete',
      name: 'Delete'
    }];

    this.$get = function () {
      return actions;
    };
  });
})(angular, CRM.$, CRM._);
