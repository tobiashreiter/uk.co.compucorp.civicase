(function (angular, $, _) {
  var module = angular.module('my-activities', ['ngRoute']).config(
      [ '$routeProvider', 
         function ($routeProvider) {
            $routeProvider.when('/', {
                reloadOnSearch: false,
                resolve: {
                    hiddenFilters: function () {}
                },
                template: '<civicase-my-activities></civicase-my-activities>'
            });
         }
      ]);
})(angular, CRM.$, CRM._);
