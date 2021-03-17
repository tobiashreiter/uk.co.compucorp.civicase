var moment = require('moment');

const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');
const casesService = require('./case.service.js');
const contactsService = require('./contact.service.js');

const service = {
  setupData
};

/**
 * Create Activities
 */
function setupData () {
  var caseId = casesService.caseIds[0];

  var fileUploadActivityID = createActivities(1, caseId, 'File Upload')[0];
  createAttachment(fileUploadActivityID);

  createActivities(30, caseId, 'Follow up');

  console.log('Activities data setup successful.');
}

/**
 * @param {number} numberOfActivities number of activities
 * @param {number} caseId case id
 * @param {string} activityTypeID activity type id
 * @returns {Array} list of activities
 */
function createActivities (numberOfActivities, caseId, activityTypeID) {
  var createUniqueActivity = createUniqueRecordFactory('Activity', ['subject']);

  var activityIds = [];

  for (var i = 0; i < numberOfActivities; i++) {
    activityIds.push(createUniqueActivity({
      activity_type_id: activityTypeID,
      case_id: caseId,
      source_contact_id: contactsService.activeContact.id,
      subject: activityTypeID + ' ' + (i === 0 ? '' : (i + 1)),
      activity_date_time: moment().startOf('month').format('YYYY-MM-DD')
    }).id);
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

module.exports = service;
