const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');
const casesService = require('./case.service.js');

const service = {
  setupData
};

/**
 * Create Tags
 */
function setupData () {
  var createUniqueTag = createUniqueRecordFactory('Tag', ['name', 'used_for']);
  var createUniqueEntityTag = createUniqueRecordFactory('EntityTag', ['entity_id', 'entity_table', 'tag_id']);
  var caseTag = 'Backstop Case Tag';
  var caseId = casesService.caseIds[0];

  createUniqueTag({
    is_selectable: 1,
    name: caseTag,
    used_for: 'Cases'
  });

  createUniqueEntityTag({
    entity_id: caseId,
    entity_table: 'civicrm_case',
    tag_id: caseTag
  });

  console.log('Tags data setup successful.');
}

module.exports = service;
