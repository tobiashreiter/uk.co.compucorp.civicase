// const cvApi = require('../utils/cv-api.js');

// const service = {
//   setupData
// };

// /**
//  * Enable Finance Management for Awards
//  */
// function setupData () {
//   enableFinanceManagementFor();
// }

// /**
//  * Enable Civicase Component
//  * Required for scenarios in 'civicase.json'
//  */
// function enableFinanceManagementFor () {
//   var setting = cvApi('Setting', 'get', {
//     sequential: true
//   }).values[0];

//   setting.case_category_finance_management = {
//     "2": "1"
//   };

//   var enableFinanceManagement = cvApi('Setting', 'create', setting);

//   if (!enableFinanceManagement.is_error) {
//     console.log('Finance Management enabled for awards.');
//   }
// }

// module.exports = service;
