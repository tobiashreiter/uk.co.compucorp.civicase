<?php

use CRM_Core_BAO_CustomGroup as CustomGroup;

/**
 * Default instance custom group post process helper class.
 */
abstract class CRM_Civicase_Helper_InstanceCustomGroupPostProcess {

  /**
   * Returns case type ID's for a case category.
   *
   * @param CRM_Core_BAO_CustomGroup $customGroup
   *   Custom Group Object.
   * @param int $caseTypeCategoryId
   *   Case Type Category ID.
   *
   * @return array
   *   Case Type Ids.
   */
  public function getCaseTypeIdsForCaseCategory(CustomGroup $customGroup, $caseTypeCategoryId) {
    $result = civicrm_api3('CaseType', 'get', [
      'sequential' => 1,
      'return' => ['id'],
      'case_type_category' => $caseTypeCategoryId,
    ]);

    return array_column($result['values'], 'id');
  }

}
