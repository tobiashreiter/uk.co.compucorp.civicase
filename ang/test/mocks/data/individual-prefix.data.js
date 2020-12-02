(function (angular, CRM) {
  var module = angular.module('civicase.data');

  CRM['civicase-base'].individualPrefix = {
    1: {
      value: '1',
      label: 'Mrs.',
      name: 'Mrs.',
      is_active: '1',
      weight: '1',
      filter: '0'
    },
    2: {
      value: '2',
      label: 'Ms.',
      name: 'Ms.',
      is_active: '1',
      weight: '2',
      filter: '0'
    },
    3: {
      value: '3',
      label: 'Mr.',
      name: 'Mr.',
      is_active: '1',
      weight: '3',
      filter: '0'
    },
    4: {
      value: '4',
      label: 'Dr.',
      name: 'Dr.',
      is_active: '1',
      weight: '4',
      filter: '0'
    }
  };

  module.constant('IndividualPrefixData', {
    values: CRM['civicase-base'].individualPrefix
  });
})(angular, CRM);
