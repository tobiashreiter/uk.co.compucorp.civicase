const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');
const caseTypeService = require('./case-type.service.js');
const contactService = require('./contact.service.js');

const service = {
  setupData,
  caseSubject: 'Backstop Case',
  emptyCaseSubject: 'Backstop Empty Case',
  caseIds: []
};

/**
 * Create Cases
 */
function setupData () {
  service.caseIds = service.caseIds.concat(
    createCases(
      17,
      service.caseSubject,
      caseTypeService.caseType,
      contactService.activeContact
    )
  );
  service.caseIds = service.caseIds.concat(
    createCases(
      1,
      service.emptyCaseSubject,
      caseTypeService.caseType,
      contactService.emptyContact
    )
  );

  console.log('Case data setup successful.');
}

/**
 * Create Cases
 *
 * @param {number} numberOfCases number of cases
 * @param {object} caseSubject case subject
 * @param {object} caseType case type
 * @param {object} contact contact object
 * @returns {Array} list of case ids
 */
function createCases (numberOfCases, caseSubject, caseType, contact) {
  var createUniqueCase = createUniqueRecordFactory('Case', ['subject']);

  var caseIds = [];

  for (var i = 0; i < numberOfCases; i++) {
    caseIds.push(createUniqueCase({
      case_type_id: caseType.id,
      contact_id: contact.id,
      creator_id: contact.id,
      subject: caseSubject + (i === 0 ? '' : (i + 1))
    }).id);
  }

  return caseIds;
}

module.exports = service;
