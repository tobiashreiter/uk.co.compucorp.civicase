(function (angular, $, _) {
  var module = angular.module('casetype');

  module.config(function ($routeProvider, UrlParametersProvider) {
    $routeProvider.when('/list', {
      template: function () {
        var urlParams = UrlParametersProvider.parse(window.location.search);

        return '<casetype-list case-type-category="' + urlParams.case_type_category + '"></casetype-list>';
      }
    });
  });
})(angular, CRM.$, CRM._);
