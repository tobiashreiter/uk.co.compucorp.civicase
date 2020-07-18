<?php

use Civi\CCase\Utils as CaseUtils;

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
    if (!is_array($contactInvolved)) {
      $contactInvolved = ['=' => $contactInvolved];
    }

    $caseClient = CRM_Core_DAO::createSQLFilter('contact_id', $contactInvolved);
    $nonCaseClient = CRM_Core_DAO::createSQLFilter('involved.id', $contactInvolved);

    CaseUtils::joinOnRelationship($query, 'involved');
    $query->where("a.id IN (SELECT case_id FROM civicrm_case_contact WHERE ($nonCaseClient OR $caseClient))");
  }

}
