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
function _civicrm_api3_case_getrelations_spec(array &$spec) {
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
function civicrm_api3_case_getrelations(array $params) {
  $relations = [];
  $params += ['options' => []];
  $caseContacts = civicrm_api3('CaseContact', 'get', [
    'case_id' => $params['case_id'],
    'contact_id.is_deleted' => 0,
    'return' => 'contact_id',
    'options' => ['limit' => 0],
  ]);
  $clientIds = CRM_Utils_Array::collect('contact_id', $caseContacts['values']);
  $relationshipParams = [
    'is_active' => 1,
    'relationship_type_id.is_active' => 1,
    'case_id' => ['IS NULL' => 1],
    "contact_id_a" => ['IN' => $clientIds],
    "contact_id_b" => ['IN' => $clientIds],
    'options' => ['or' => [["contact_id_a", "contact_id_b"]]] + $params['options'],
    'return' => [
      'relationship_type_id',
      'contact_id_a',
      'contact_id_b',
      'description',
    ],
  ];
  $result = civicrm_api3('Relationship', 'get', $relationshipParams);

  foreach ($result['values'] as $relation) {
    $a = in_array($relation['contact_id_a'], $clientIds) ? 'b' : 'a';
    $b = in_array($relation['contact_id_a'], $clientIds) ? 'a' : 'b';
    $contactIds[$relation["contact_id_$a"]] = $relation["contact_id_$a"];
    $relations[] = [
      'id' => $relation["contact_id_$a"],
      'client_contact_id' => $relation["contact_id_$b"],
      'relationship_id' => $relation['id'],
      'relationship_type_id' => $relation['relationship_type_id'],
      'relationship_description' => $relation['description'],
      'relationship_direction' => "{$a}_{$b}",
    ];
  }

  if (!$relations) {
    return $result;
  }

  unset($params['case_id'], $params['options']);
  $contacts = civicrm_api3('Contact', 'get', [
    'sequential' => 0,
    'id' => ['IN' => $contactIds],
    'options' => ['limit' => 0],
  ]);

  foreach ($relations as &$relation) {
    $relation += $contacts['values'][$relation['id']];
  }

  $out = civicrm_api3_create_success(array_filter($relations), $params, 'Case', 'getrelations');
  $out['count'] = $result['count'];

  return $out;
}
