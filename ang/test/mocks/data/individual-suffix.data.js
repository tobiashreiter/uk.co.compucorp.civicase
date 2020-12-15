(function (angular, CRM) {
  var module = angular.module('civicase.data');

  CRM['civicase-base'].individualSuffix = {
    1: {
      value: '1',
      label: 'Jr.',
      name: 'Jr.',
      is_active: '1',
      weight: '1',
      filter: '0'
    },
    2: {
      value: '2',
      label: 'Sr.',
      name: 'Sr.',
      is_active: '1',
      weight: '2',
      filter: '0'
    },
    3: {
      value: '3',
      label: 'II',
      name: 'II',
      is_active: '1',
      weight: '3',
      filter: '0'
    },
    4: {
      value: '4',
      label: 'III',
      name: 'III',
      is_active: '1',
      weight: '4',
      filter: '0'
    },
    5: {
      value: '5',
      label: 'IV',
      name: 'IV',
      is_active: '1',
      weight: '5',
      filter: '0'
    },
    6: {
      value: '6',
      label: 'V',
      name: 'V',
      is_active: '1',
      weight: '6',
      filter: '0'
    },
    7: {
      value: '7',
      label: 'VI',
      name: 'VI',
      is_active: '1',
      weight: '7',
      filter: '0'
    },
    8: {
      value: '8',
      label: 'VII',
      name: 'VII',
      is_active: '1',
      weight: '8',
      filter: '0'
    }
  };

  module.constant('IndividualSuffixData', {
    values: CRM['civicase-base'].individualSuffix
  });
})(angular, CRM);
