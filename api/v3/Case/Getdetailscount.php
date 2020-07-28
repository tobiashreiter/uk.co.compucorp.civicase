<?php

/**
 * @file
 * Case.getdetailscount file.
 */

use CRM_Civicase_APIHelpers_CaseDetails as CaseDetailsQuery;
use CRM_Civicase_APIHelpers_ExtendedApi3SelectQuery as ExtendedApi3SelectQuery;

/**
 * Case getdetailscount API function.
 *
 * Provides a count of cases but properly respects filters unlike `getcount`.
 *
 * @param array $params
 *   List of parameters to use for filtering.
 *
 * @return array
 *   API result.
 *
 * @throws API_Exception
 */
function civicrm_api3_case_getdetailscount(array $params) {
  $params['options'] = CRM_Utils_Array::value('options', $params, []);
  $params['options']['is_count'] = 1;

  // Remove unnecesary parameters:
  unset($params['return'], $params['sequential']);

  list('params' => $params, 'sql' => $sql) = CaseDetailsQuery::get($params);
  $query = _civicrm_api3_case_getdetailscount_get_count_query($params);
  $query->merge($sql);
  $query->setSelectFields(['COUNT(DISTINCT(a.id))' => 'CASE_DETAILS_COUNT']);
  $results = $query->run();

  return array_shift($results)['CASE_DETAILS_COUNT'];
}

/**
 * Returns the API query to get the case details count.
 *
 * The query is based on the given API parameters.
 *
 * @param array $params
 *   List of parameters to use for filtering.
 *
 * @return CRM_Civicase_APIHelpers_ExtendedApi3SelectQuery
 *   Query Object reference.
 */
function _civicrm_api3_case_getdetailscount_get_count_query(array $params) {
  $entityName = 'Case';
  $options = _civicrm_api3_get_options_from_params($params);

  $query = new ExtendedApi3SelectQuery($entityName, CRM_Utils_Array::value(
    'check_permissions', $params, FALSE));
  $query->where = $params;

  return $query;
}
