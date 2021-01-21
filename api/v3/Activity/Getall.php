<?php

/**
 * @file
 * Activity.GetAll file.
 */

/**
 * Activity.GetAll API specification.
 *
 * @param array $spec
 *   Description of fields supported by this API call.
 */
function _civicrm_api3_activity_getall_spec(array &$spec) {
  $spec = civicrm_api3('Activity', 'getfields')['values'];
}

/**
 * Activity.GetAll API.
 *
 * This API is similar to “Activity.get”. But if the 'target_contact_id' are
 * more than 25, it returns first 25 only. Also adds a
 * flag `target_contacts_are_limited` to denote, that contacts are hidden.
 *
 * @param array $params
 *   API parameters.
 *
 * @return array
 *   Activities
 */
function civicrm_api3_activity_getall(array $params) {
  $result = civicrm_api3('Activity', 'get', $params);

  if (!$result['is_error']) {
    foreach ($result['values'] as &$record) {
      $targetContactIds = $record['target_contact_id'];
      // Change this to 25.
      $limitTargetContactIdsTo = 3;

      if (!empty($targetContactIds) && count($targetContactIds) > $limitTargetContactIdsTo) {
        $record['target_contact_id'] = array_slice($record['target_contact_id'], 0, $limitTargetContactIdsTo, TRUE);
        $record['target_contact_name'] = array_slice($record['target_contact_name'], 0, $limitTargetContactIdsTo, TRUE);
        $record['target_contact_sort_name'] = array_slice($record['target_contact_sort_name'], 0, $limitTargetContactIdsTo, TRUE);
        $record['target_contacts_are_limited'] = '1 ';
      }
    }
  }

  return $result;
}
