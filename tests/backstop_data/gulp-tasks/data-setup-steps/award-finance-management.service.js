// var _ = require('lodash');
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

//   console.log(setting);
//   // const civicaseComponent = "CiviCase";
//   // var isCiviCaseComponentEnabled = _.indexOf(setting.enable_components, civicaseComponent) !== -1;

//   // if (isCiviCaseComponentEnabled) {
//   //   return;
//   // }

//   // setting.enable_components.push(civicaseComponent)

//   // var enableCivicaseComponent = cvApi('Setting', 'create', setting);

//   // if (!enableCivicaseComponent.is_error) {
//   //   console.log('CiviCase component enabled.');
//   // }
// }

// module.exports = service;
