(function (angular, $, _) {
  var module = angular.module('civicase-base');

  module.directive('civicaseIncludeReplace', function () {
    return {
      require: 'ngInclude',
      link: function (scope, el, attrs) {
        el.replaceWith(el.children());
      }
    };
  });
})(angular, CRM.$, CRM._);
