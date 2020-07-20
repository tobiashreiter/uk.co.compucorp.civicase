<?php

use Civi\CCase\Utils as CiviCaseUtils;

/**
 * Case By Contact Involved API Helper class.
 */
class CRM_Civicase_APIHelpers_CasesByContactInvolved {

  /**
   * Filters the given case query by contact involvement.
   *
   * @param CRM_Utils_SQL_Select $query
   *   The SQL object reference.
   * @param int|array $contactInvolved
   *   The ID of the contact related to the case.
   */
  public static function filter(CRM_Utils_SQL_Select $query, $contactInvolved) {
    $contactInvolvedFilter = CRM_Civicase_APIHelpers_Filters::normalize($contactInvolved);
    $caseContactSqlFilter = self::getCaseContactSqlFilter($contactInvolvedFilter);

    CiviCaseUtils::joinOnRelationship($query, 'involved');
    $query->where($caseContactSqlFilter);
  }

  /**
   * Case Contact SQL Statement filter.
   *
   * @param array $contactInvolved
   *   Involved contact filter.
   *
   * @return string
   *   Returns a SQL statement for filtering cases by involved contact.
   */
  public static function getCaseContactSqlFilter(array $contactInvolved) {
    $caseClient = CRM_Core_DAO::createSQLFilter('contact_id', $contactInvolved);
    $nonCaseClient = CRM_Core_DAO::createSQLFilter('involved.id', $contactInvolved);

    return "a.id IN (SELECT case_id FROM civicrm_case_contact WHERE ($nonCaseClient OR $caseClient))";
  }

  /**
   * Activity Contact SQL Statement filter.
   *
   * @param array $contactInvolved
   *   Involved contact filter.
   *
   * @return string
   *   Returns a SQL statement for filtering cases by activity contact.
   */
  public static function getActivityContactSqlFilter(array $contactInvolved) {
    $activityContactCondition = CRM_Core_DAO::createSQLFilter('civicrm_activity_contact.contact_id', $contactInvolved);

    return "a.id IN (SELECT DISTINCT(case_id) FROM civicrm_case_activity WHERE activity_id IN (
      SELECT DISTINCT(civicrm_activity.id) FROM civicrm_activity
        INNER JOIN civicrm_activity_contact
        ON civicrm_activity.id = civicrm_activity_contact.activity_id
        WHERE civicrm_activity.is_deleted = 0
          AND civicrm_activity.is_current_revision = 1
          AND civicrm_activity.is_test = 0
          AND $activityContactCondition))";
  }

}
