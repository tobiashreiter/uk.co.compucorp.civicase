<?php

class CRM_Civicase_APIHelpers_CasesByContactInvolved {
  /**
   * Adds joins and conditions to the given query in order to filter cases by
   * contact involvement.
   *
   * @param CRM_Utils_SQL_Select $query the SQL object reference.
   * @param Int|Array $contactInvolved the ID of the contact related to the case.
   */
  static function filter($query, $contactInvolved) {
    if (!is_array($contactInvolved)) {
      $contactInvolved = ['=' => $contactInvolved];
    }

    $caseClient = CRM_Core_DAO::createSQLFilter('contact_id', $contactInvolved);
    $nonCaseClient = CRM_Core_DAO::createSQLFilter('involved.id', $contactInvolved);

    \Civi\CCase\Utils::joinOnRelationship($query, 'involved');
    $query->where("a.id IN (SELECT case_id FROM civicrm_case_contact WHERE ($nonCaseClient OR $caseClient))");
  }
}
