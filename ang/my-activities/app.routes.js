(function (angular) {
  var module = angular.module('my-activities');

  module.config(function ($routeProvider) {
    $routeProvider.when('/', {
      reloadOnSearch: false,
      resolve: {
        hiddenFilters: function () {}
      },
      template: '<civicase-my-activities></civicase-my-activities>'
    });
  });
})(angular);
