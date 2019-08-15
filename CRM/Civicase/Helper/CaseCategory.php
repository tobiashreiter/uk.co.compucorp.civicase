<?php

use CRM_Case_BAO_CaseType as CaseType;

/**
 * CRM_Civicase_Helper_CaseCategory class.
 */
class CRM_Civicase_Helper_CaseCategory {

  const CASE_TYPE_CATEGORY_NAME = 'Cases';

  /**
   * Returns the case category name for the case Id.
   *
   * @param int $caseId
   *   The Case ID.
   *
   * @return string|null
   *   The Case Category Name.
   */
  public static function getCategoryName($caseId) {
    $caseTypeCategories = CaseType::buildOptions('case_type_category');

    $result = civicrm_api3('Case', 'getsingle', [
      'id' => $caseId,
      'return' => ['case_type_id'],
      'api.CaseType.getsingle' => [
        'id' => '$value.case_type_id',
        'return' => ['case_type_category'],
      ],
    ]);

    if (!empty($result['api.CaseType.getsingle']['case_type_category'])) {
      $caseCategoryId = $result['api.CaseType.getsingle']['case_type_category'];

      return $caseTypeCategories[$caseCategoryId];
    }

    return NULL;
  }

  /**
   * Returns the case types for the cases category.
   *
   * @return array
   *   Array of Case Types indexed by Id.
   */
  public static function getCaseTypesForCase() {
    $result = civicrm_api3('CaseType', 'get', [
      'sequential' => 1,
      'return' => ['title', 'id'],
      'case_type_category' => self::CASE_TYPE_CATEGORY_NAME,
    ]);

    return array_column($result['values'], 'title', 'id');
  }

}
