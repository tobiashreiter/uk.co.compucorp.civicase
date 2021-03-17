const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');

const service = {
  setupData,
  benefitsSpecialistRelType: null,
  homelessCoordinatorRelType: null,
  relationshipTypeNames: {
    homelessCoordinator: 'Homeless Services Coordinator is',
    healthServiceCoordinator: 'Health Services Coordinator',
    benefitsSpecialist: 'Benefits Specialist is'
  }
};

/**
 * Create Relationship Types
 */
function setupData () {
  service.homelessCoordinatorRelType = createRelationshipTypes(service.relationshipTypeNames.homelessCoordinator);
  service.benefitsSpecialistRelType = createRelationshipTypes(service.relationshipTypeNames.benefitsSpecialist);

  console.log('Relationship Type data setup successful.');
}

/**
 * Create Relationship Types
 *
 * @param {string} nameAB relationship name
 * @returns {Array} list of activity ids
 */
function createRelationshipTypes (nameAB) {
  var createUniqueRelationshipType = createUniqueRecordFactory('RelationshipType', ['name_a_b']);

  return createUniqueRelationshipType({
    name_a_b: nameAB
  });
}

module.exports = service;
