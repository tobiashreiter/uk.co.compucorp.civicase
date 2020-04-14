/* eslint-env jasmine */

(function () {
  var crmUtilModule = angular.module('crmUtil');
  var civicaseBaseModule = angular.module('civicase-base');

  var mockCrmApi = ['$q', function mockCrmApi ($q) {
    var crmApi = jasmine.createSpy('crmApi');
    crmApi.and.returnValue($q.resolve());
    return crmApi;
  }];

  crmUtilModule.factory('crmApi', mockCrmApi);
  civicaseBaseModule.factory('crmApi', mockCrmApi);
})();
