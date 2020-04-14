(function (angular, $, _) {
  var module = angular.module('civicase-base');

  /**
   * The original crmApi, which is part of `crmUtil` module, does not 'reject'
   * the promise when an array of api requests are sent, and one of them got
   * returned with error message. This decorator fixes that.
   */
  module.decorator('crmApi', function ($delegate, $q) {
    var crmApi = function (entity, action, params, message) {
      var deferred = $q.defer();

      $delegate(entity, action, params, message)
        .then(function (result) {
          var isError = false;

          if (_.isArray(result)) {
            _.each(result, function (response) {
              if (response.is_error) {
                isError = true;
              }
            });
          }

          isError ? deferred.reject(result) : deferred.resolve(result);
        });

      return deferred.promise;
    };

    crmApi.val = $delegate.val;

    return crmApi;
  });
})(angular, CRM.$, CRM._);
