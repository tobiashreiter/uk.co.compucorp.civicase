const cvApi = require('./cv-api.js');
var execSync = require('child_process').execSync;

const CONFIGS = require('./configs.js');
const getActiveCaseId = require('./get-active-case-id.js');
const casesService = require('../data-setup-steps/case.service.js');

var CACHE = {
  caseId: null,
  emptyCaseId: null,
  contactIdsMap: {}
};

/**
 * Runs a series of URL var replaces for the scenario URL. A URL var would look
 * like `{url}/contact` and can be replaced into a string similar to
 * `http://example.com/contact`.
 *
 * @param {string} url the original scenario url with all vars intact.
 * @returns {string} the scenario url with vars replaced.
 */
function replaceUrlVars (url) {
  const config = CONFIGS.getSiteConfig();

  var URL_VAR_REPLACERS = [
    replaceCaseIdVar,
    replaceEmptyCaseIdVar,
    replaceRootUrlVar,
    replaceContactIdVar
  ];

  URL_VAR_REPLACERS.forEach(function (urlVarReplacer) {
    url = urlVarReplacer(url, config);
  });

  return url;
}

/**
 * Tries to get the record id from the cache first and if not found will retrieve
 * it using `cv api`, store the record id, and return it.
 *
 * @param {string} cacheKey the cache key where the record id is stored.
 * @param {Function} callback a callback function that should return the record id
 *   if none is stored.
 * @returns {string} if the record from cache
 */
function getRecordIdFromCacheOrCallback (cacheKey, callback) {
  if (!CACHE[cacheKey]) {
    CACHE[cacheKey] = callback();
  }

  return CACHE[cacheKey];
}

/**
 * Replaces the `{caseId}` var with the id of the first non deleted, open case.
 *
 * @param {string} url the scenario url.
 * @param {object} config the site config options.
 * @returns {string} replaced record id
 */
function replaceCaseIdVar (url, config) {
  return url.replace('{caseId}', function () {
    return getRecordIdFromCacheOrCallback('caseId', getActiveCaseId);
  });
}

/**
 * Replaces the `{emptyCaseId}` var with the id for the empty case created by the setup script.
 *
 * @param {string} url the scenario url.
 * @returns {string} case record id
 */
function replaceEmptyCaseIdVar (url) {
  return url.replace('{emptyCaseId}', function () {
    return getRecordIdFromCacheOrCallback('emptyCaseId', () => {
      var caseRecord = cvApi('Case', 'get', {
        subject: casesService.emptyCaseSubject
      });

      return caseRecord.id;
    });
  });
}

/**
 * Replaces the `{contactName: CONTACT NAME}` var with the contact id for the contact.
 *
 * @param {string} url the scenario url.
 * @param {object} config the site config options.
 * @returns {string} final processed url string
 */
function replaceContactIdVar (url, config) {
  return url.replace(/{contactName:(.+)}/, function (stringMatch, contactName) {
    var contactId = CACHE.contactIdsMap[contactName];

    if (!contactId) {
      var cmd = `cv api contact.getsingle display_name=${contactName} option.limit=1`;
      var contactInfo = JSON.parse(execSync(cmd, { cwd: config.root }));
      contactId = contactInfo.id;
      CACHE.contactIdsMap[contactName] = contactId;
    }

    return contactId;
  });
}

/**
 * Replaces the `{url}` var with the site url as defined in the config file.
 *
 * @param {string} url the scenario url.
 * @param {object} config the site config options.
 * @returns {string} final processed url string
 */
function replaceRootUrlVar (url, config) {
  return url.replace('{url}', config.url);
}

module.exports = replaceUrlVars;
