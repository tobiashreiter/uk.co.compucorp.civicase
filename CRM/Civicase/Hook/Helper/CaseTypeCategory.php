<?php

class CRM_Civicase_Hook_Helper_CaseTypeCategory {

  /**
   * Returns the case category for a case.
   *
   * @param int $caseTypeId
   *
   * @return int|null
   */
  public function getCategory($caseTypeId) {
    $caseCategory = NULL;

    if (empty($caseTypeId)) {
      return $caseCategory;
    }

    $result = civicrm_api3('CaseType', 'getsingle', [
      'return' => ['case_type_category'],
      'id' => $caseTypeId,
    ]);

    if (!empty($result['case_type_category'])) {
      $caseCategory = $result['case_type_category'];
    }

    return $caseCategory;
  }
}
