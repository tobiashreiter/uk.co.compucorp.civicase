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

  $relationshipParams = _prepare_relationship_params($params, $clientIds);
  if (!$relationshipParams) {
    return civicrm_api3_create_success([], $params, 'Case', 'getrelations');
  }

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

  $contacts = civicrm_api3('Contact', 'get', [
    'sequential' => 0,
    'id' => ['IN' => $contactIds],
    'options' => $params['options']['limit'],
  ])['values'];

  foreach ($relations as &$relation) {
    $relation += $contacts[$relation['id']];
  }

  $out = civicrm_api3_create_success(array_filter($relations), $params, 'Case', 'getrelations');
  $out['count'] = $result['count'];

  return $out;
}

/**
 * Get parameters for Relationship.get api call.
 *
 * <If (display_name) param is not passed>
 *  Return Relationships, where either (Contact ID A) = (Client ID),
 *  or (Contact ID B) = (Client ID).
 * <Else = (display_name) param is passed>.
 *  Fetch the Contacts where (ID) = (Client ID), and
 *  (display_name) = (passed display_name)
 *    <If the Number of Contacts > 0>
 *      Return Relationships, where either (Contact ID A) = (Fetched Contacts),
 *      OR (Contact ID B) = (Client ID)
 *    <Else>
 *      Fetch All the contacts where (display_name) = (passed display_name)
 *        <If the Number of Contacts > 0>
 *          Return Relationships, where (Contact ID A) = (Fetched Contacts),
 *          AND (Contact ID B) = Client ID
 *        <Else>
 *          Return Empty Array, as no relationships are fine.
 *
 * @param array $params
 *   Parameters.
 * @param array $clientIds
 *   Parameters.
 *
 * @return array|bool
 *   Parameters.
 */
function _prepare_relationship_params(array $params, array $clientIds) {
  $isDisplayNameFilterPresent = !empty($params['display_name']);
  // Default params.
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

  if ($isDisplayNameFilterPresent) {
    $contacts = civicrm_api3('Contact', 'get', [
      'sequential' => 0,
      'id' => ['IN' => $clientIds],
      'display_name' => $params['display_name'],
      // ClientIDs wont be more than 25 usually, so wont be a performance issue.
      'options' => ['limit' => 0],
    ])['values'];

    if (count($contacts) > 0) {
      // Contact ID A can be any one of the Contacts fetched previously.
      $contactIds = array_column($contacts, 'id');
      $relationshipParams["contact_id_a"] = ['IN' => $contactIds];
    }
    else {
      $contacts = civicrm_api3('Contact', 'get', [
        'sequential' => 0,
        'display_name' => $params['display_name'],
        'options' => ['limit' => 0],
      ])['values'];
      $contactIds = array_column($contacts, 'id');

      if (count($contactIds) > 0) {
        $relationshipParams["contact_id_a"] = ['IN' => $contactIds];
        $relationshipParams["options"] = $params['options'];
      }
      else {
        return FALSE;
      }
    }
  }

  return $relationshipParams;
}
