/* eslint-env jasmine */

(function () {
  var module = angular.module('civicase');

  module.factory('civicaseCrmApi', ['$q', function ($q) {
    var civicaseCrmApi = jasmine.createSpy('civicaseCrmApi');
    civicaseCrmApi.and.returnValue($q.resolve());

    return civicaseCrmApi;
  }]);
})();
