const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');
const casesService = require('./case.service.js');
const contactsService = require('./contact.service.js');
const relationshipTypesService = require('./relationship-type.service.js');

const service = {
  setupData
};

/**
 * Create Relationships
 */
async function setupData () {
  var caseId = casesService.caseIds[0];

  await createRelationship(
    caseId,
    contactsService.activeContact.id,
    contactsService.emptyContact.id,
    relationshipTypesService.benefitsSpecialistRelType.id,
    'Manager Role Assigned'
  );

  await createRelationship(
    caseId,
    contactsService.activeContact.id,
    contactsService.emptyContact.id,
    relationshipTypesService.homelessCoordinatorRelType.id,
    'Homeless Coordinator Assigned'
  );

  console.log('Relationship data setup successful.');
}

/**
 * Create Relationship
 *
 * @param {number} caseID case id
 * @param {number} contactIdA contact id A
 * @param {number} contactIdB contact id B
 * @param {number} relationshipTypeId relationship type id
 * @param {string} description relationship description
 * @returns {object} relationship
 */
async function createRelationship (caseID, contactIdA, contactIdB, relationshipTypeId, description) {
  var createUniqueRelationship = createUniqueRecordFactory('Relationship', ['contact_id_a', 'contact_id_b', 'relationship_type_id']);

  createUniqueRelationship({
    contact_id_a: contactIdA,
    relationship_type_id: relationshipTypeId,
    start_date: 'now',
    end_date: null,
    contact_id_b: contactIdB,
    case_id: caseID,
    description: description
  });

  await sleep(500);
}

/**
 * @param {number} ms milliseconds
 */
async function sleep (ms) {
  await new Promise((resolve) => setTimeout(resolve, ms));
}

module.exports = service;
