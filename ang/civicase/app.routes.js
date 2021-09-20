(function (angular, $, _) {
  var module = angular.module('civicase');

  module.config(function ($routeProvider) {
    $routeProvider.when('/case/list', {
      reloadOnSearch: false,
      resolve: {
        hiddenFilters: function () {}
      },
      templateUrl: '~/civicase/case/list/directives/case-list.html'
    });
  });

  module.config(function ($routeProvider, UrlParametersProvider) {
    $routeProvider.when('/case', {
      reloadOnSearch: false,
      template: function () {
        var urlParams = UrlParametersProvider.parse(window.location.search);
        var urlHash = UrlParametersProvider.parse(window.location.hash);

        if (urlParams.case_type_category === urlHash.case_type_category) {
          return '<civicase-dashboard></civicase-dashboard>';
        }

        return '<civicase-access-denied></civicase-access-denied>';
      }
    });
  });

  module.config(function ($routeProvider) {
    $routeProvider.when('/activity/feed', {
      reloadOnSearch: false,
      template: '<div id="bootstrap-theme" class="civicase__container" ' +
        'civicase-activity-feed="{}" hide-quick-nav-when-details-is-visible="true"></div>'
    });
  });

  module.config(function ($routeProvider) {
    $routeProvider.when('/case/search', {
      reloadOnSearch: false,
      template: '<h1 crm-page-title>{{ ts(\'Find Cases\') }}</h1>' +
      '<div id="bootstrap-theme" class="civicase__container">' +
      '<div class="panel" civicase-search="selections" expanded="true" on-search="show(selectedFilters)">' +
      '</div>' +
      '<pre>{{selections|json}}</pre>' +
      '</div>',
      controller: 'civicaseSearchPageController'
    });
  });
})(angular, CRM.$, CRM._);
