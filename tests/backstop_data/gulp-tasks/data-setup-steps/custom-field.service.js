const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');
const customGroupService = require('./custom-group.service.js');

const service = {
  setupData
};

/**
 * Create Custom Fields
 */
function setupData () {
  createCustomField(customGroupService.inline.id, customGroupService.inline.fieldLabel);
  createCustomField(customGroupService.tab.id, customGroupService.tab.fieldLabel);

  console.log('Custom Fields data setup successful.');
}

/**
 * Create Custom Field
 *
 * @param {string} customGroupId custom group id
 * @param {string} label label
 * @returns {object} custom field
 */
function createCustomField (customGroupId, label) {
  var createUniqueCustomField = createUniqueRecordFactory('CustomField', ['label']);

  return createUniqueCustomField({
    custom_group_id: customGroupId,
    label: label,
    data_type: 'String',
    html_type: 'Text'
  });
}

module.exports = service;
