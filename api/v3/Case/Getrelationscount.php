<?php

/**
 * @file
 * Case.getrelations file.
 */

require_once 'api/v3/Contact.php';

/**
 * Case.Getrelations API specification.
 *
 * @param array $spec
 *   description of fields supported by this API call.
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_case_getrelationscount_spec(array &$spec) {
  _civicrm_api3_contact_get_spec($spec);
  $spec['case_id'] = [
    'title' => 'Case ID',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => TRUE,
  ];
}

/**
 * Case.Getrelations API.
 *
 * Perform a search for contacts related to clients of a case.
 *
 * @param array $params
 *   Parameters.
 *
 * @return array
 *   API result.
 *
 * @throws API_Exception
 */
function civicrm_api3_case_getrelationscount(array $params) {
  $params['options'] = CRM_Utils_Array::value('options', $params, ['limit' => 0]);
  $params['options']['is_count'] = 1;

  return civicrm_api3('Case', 'getrelations', $params);
}
