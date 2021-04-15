/* eslint no-param-reassign: "error" */

(function () {
  var module = angular.module('civicase.data');

  CRM['civicase-base'].caseTypeCategories = {
    1: {
      value: '1',
      label: 'Cases',
      name: 'Cases',
      is_active: '1',
      custom_fields: {
        singular_label: 'Case'
      }
    },
    2: {
      value: '2',
      label: 'Prospecting',
      name: 'Prospecting',
      is_active: '1',
      custom_fields: {
        singular_label: 'Prospecting'
      }
    },
    3: {
      value: '3',
      label: 'Awards',
      name: 'awards',
      is_active: '1',
      custom_fields: {
        singular_label: 'Award'
      }
    }
  };

  module.constant('caseTypeCategoriesMockData', CRM['civicase-base'].caseTypeCategories);
}(CRM));
