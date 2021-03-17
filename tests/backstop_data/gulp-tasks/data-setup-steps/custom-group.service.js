const createUniqueRecordFactory = require('../utils/create-unique-record-factory.js');

const service = {
  setupData,
  inline: {
    id: null,
    fieldLabel: 'Backstop Case Inline Custom Field',
    groupTitle: 'Backstop Case Inline Custom Group'
  },
  tab: {
    id: null,
    fieldLabel: 'Backstop Case Tab Custom Field',
    groupTitle: 'Backstop Case Tab Custom Group'
  }
};

/**
 * Create Custom Groups
 */
function setupData () {
  service.inline.id = createCustomGroup('Inline', service.inline.groupTitle).id;
  service.tab.id = createCustomGroup('Tab', service.tab.groupTitle).id;

  console.log('Custom Group data setup successful.');
}

/**
 * Create Custom Groups
 *
 * @param {string} style style
 * @param {string} title title
 * @returns {object} custom group
 */
function createCustomGroup (style, title) {
  var createUniqueCustomGroup = createUniqueRecordFactory('CustomGroup', ['title']);

  return createUniqueCustomGroup({
    extends: 'Case',
    style: style,
    title: title
  });
}

module.exports = service;
