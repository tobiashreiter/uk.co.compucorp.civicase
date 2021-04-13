/* eslint no-param-reassign: "error" */

(function () {
  var module = angular.module('civicase.data');

  CRM['civicase-base'].caseTypeCategories = {
    1: {
      value: '1',
      label: 'Cases',
      name: 'Cases',
      is_active: '1',
      'api.CaseCategoryInstance.get': {
        values: [
          {
            id: '1',
            category_id: '1',
            instance_id: '1',
            singular_label: 'Case'
          }
        ]
      }
    },
    2: {
      value: '2',
      label: 'Prospecting',
      name: 'Prospecting',
      is_active: '1',
      'api.CaseCategoryInstance.get': {
        values: [
          {
            id: '2',
            category_id: '2',
            instance_id: '1',
            singular_label: 'Prospecting'
          }
        ]
      }
    },
    3: {
      value: '3',
      label: 'Awards',
      name: 'awards',
      is_active: '1',
      'api.CaseCategoryInstance.get': {
        values: [
          {
            id: '3',
            category_id: '3',
            instance_id: '2',
            singular_label: 'Award'
          }
        ]
      }
    }
  };

  module.constant('caseTypeCategoriesMockData', CRM['civicase-base'].caseTypeCategories);
}(CRM));
