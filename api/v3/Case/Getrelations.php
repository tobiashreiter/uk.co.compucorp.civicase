<?php

/**
 * @file
 * Case.getrelations file.
 */

use Civi\Api4\Relationship as Relationship;

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
  $clientIds = CRM_Utils_Array::collect('contact_id',
    civicrm_api3('CaseContact', 'get', [
      'case_id' => $params['case_id'],
      'contact_id.is_deleted' => 0,
      'return' => 'contact_id',
      'options' => ['limit' => 0],
    ])['values']
  );

  $relationships = Relationship::get()
    ->addSelect('relationship_type_id', 'contact_id_a', 'contact_id_b')
    ->addWhere('relationship_type.is_active', '=', TRUE)
    ->addWhere('is_active', '=', TRUE)
    ->addWhere('case_id', 'IS NULL');

  if ($params['display_name']) {
    $contactsFilteredByDisplayName = _get_contact_ids_by_displayname($params['display_name'], $clientIds);

    if (!$contactsFilteredByDisplayName) {
      return civicrm_api3_create_success([], $params, 'Case', 'getrelations');
    }
    _add_relationship_clause_for_display_name(
      $relationships,
      $clientIds,
      $contactsFilteredByDisplayName
    );
  }
  else {
    $relationships->addClause('OR',
      ['contact_id_a', 'IN', $clientIds], ['contact_id_b', 'IN', $clientIds]
    );
  }

  $options = _civicrm_api3_get_options_from_params($params);

  if (isset($options['limit'])) {
    $relationships->setLimit($options['limit']);
  }
  if (isset($options['offset'])) {
    $relationships->setOffset($options['offset']);
  }
  if ($options['is_count'] === 1) {
    $relationships->selectRowCount();
  }

  $result = $relationships->execute();

  if ($options['is_count'] === 1) {
    return ['count' => $result->rowCount];
  }

  foreach ($result as $relation) {
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
  ]);
  foreach ($relations as &$relation) {
    if (isset($contacts['values'][$relation['id']])) {
      $relation += $contacts['values'][$relation['id']];
    }
  }

  return civicrm_api3_create_success(array_filter($relations), $params, 'Case', 'getrelations');
}

/**
 * Get contact ids by display name.
 *
 * @param string $displayName
 *   Display Name.
 * @param array $clientIds
 *   Client IDs.
 *
 * @return array|bool
 *   API result.
 */
function _get_contact_ids_by_displayname($displayName, array $clientIds) {
  $contactsFilteredByDisplayName = civicrm_api3('Contact', 'get', [
    'sequential' => 0,
    'display_name' => $displayName,
    'id' => ['NOT IN' => $clientIds],
    'options' => ['limit' => 0],
  ])['values'];

  if (empty($contactsFilteredByDisplayName)) {
    return FALSE;
  }

  return array_column($contactsFilteredByDisplayName, 'id');
}

/**
 * Add relationship clause for display name.
 *
 * @param object $relationships
 *   Relationship API4 object.
 * @param array $clientIds
 *   Client IDs.
 * @param array $contactsFilteredByDisplayName
 *   Contacts filtered by display name.
 */
function _add_relationship_clause_for_display_name(&$relationships, array $clientIds, array $contactsFilteredByDisplayName) {
  $relationships->addClause('OR',
    [
      'AND',
      [
        ['contact_id_a', 'IN', $clientIds],
        ['contact_id_b', 'IN', $contactsFilteredByDisplayName],
      ],
    ],
    [
      'AND',
      [
        ['contact_id_a', 'IN', $contactsFilteredByDisplayName],
        ['contact_id_b', 'IN', $clientIds],
      ],
    ]
  );
}
