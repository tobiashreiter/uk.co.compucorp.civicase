const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');
const casesService = require('./case.service.js');

const service = {
  setupData
};

/**
 * Create Entity Tags
 */
function setupData () {
  var createUniqueEntityTag = createUniqueRecordFactory('EntityTag', ['entity_id', 'entity_table', 'tag_id']);
  var caseTag = 'Backstop Case Tag';

  var caseId = casesService.caseIds[0];

  createUniqueEntityTag({
    entity_id: caseId,
    entity_table: 'civicrm_case',
    tag_id: caseTag
  });

  console.log('Entity Tags data setup successful.');
}

module.exports = service;
