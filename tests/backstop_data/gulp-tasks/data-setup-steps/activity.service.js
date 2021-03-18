var moment = require('moment');
var _ = require('lodash');
const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');
const casesService = require('./case.service.js');
const contactsService = require('./contact.service.js');
const customFieldService = require('./custom-field.service.js');

const service = {
  setupData
};

/**
 * Create Activities
 */
function setupData () {
  createCaseActivities();
  createAwardsActivities();

  console.log('Activities data setup successful.');
}

/**
 * @param {number} numberOfActivities number of activities
 * @param {object} params params
 * @returns {Array} list of activities
 */
function createActivities (numberOfActivities, params) {
  var createUniqueActivity = createUniqueRecordFactory('Activity', ['subject']);

  var activityIds = [];

  for (var i = 0; i < numberOfActivities; i++) {
    var finalParams = _.extend({
      source_contact_id: contactsService.activeContact.id,
      subject: params.activity_type_id + ' ' + (i === 0 ? '' : (i + 1)),
      activity_date_time: moment().startOf('month').format('YYYY-MM-DD')
    }, params);

    activityIds.push(createUniqueActivity(finalParams).id);
  }

  return activityIds;
}

/**
 * @param {*} activityID activity ID
 */
function createAttachment (activityID) {
  var createUniqueAttachment = createUniqueRecordFactory('Attachment', ['entity_id', 'entity_table']);

  createUniqueAttachment({
    content: '',
    entity_id: activityID,
    entity_table: 'civicrm_activity',
    name: 'backstop-file-upload.png',
    mime_type: 'image/png'
  });
}

/**
 * Create Case Activities
 */
function createCaseActivities () {
  var caseId = casesService.caseIds[0];

  var fileUploadActivityID = createActivities(1, {
    case_id: caseId,
    activity_type_id: 'File Upload'
  })[0];
  createAttachment(fileUploadActivityID);

  createActivities(30, {
    case_id: caseId,
    activity_type_id: 'Follow up'
  });
}

/**
 * Create Award Application Activities
 */
function createAwardsActivities () {
  var paymentTypeFieldID = customFieldService.getCustomFieldsFor('Awards_Payment_Information', 'Type').id;
  var paymentCurrencyTypeFieldID = customFieldService.getCustomFieldsFor('Awards_Payment_Information', 'Payment_Amount_Currency_Type').id;
  var paymentAmountValueFieldID = customFieldService.getCustomFieldsFor('Awards_Payment_Information', 'Payment_Amount_Value').id;
  var awardApplicationId = casesService.awardApplicationIds[0];

  createActivities(1, {
    target_contact_id: 2,
    case_id: awardApplicationId,
    activity_type_id: 'Awards Payment',
    ['custom_' + paymentTypeFieldID]: 1,
    ['custom_' + paymentCurrencyTypeFieldID]: 'GBP',
    ['custom_' + paymentAmountValueFieldID]: '5000'
  });
}

module.exports = service;
