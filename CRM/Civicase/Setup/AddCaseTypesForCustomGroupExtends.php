<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * CRM_Civicase_Setup_CaseTypesForCustomGroupExtends class.
 */
class CRM_Civicase_Setup_AddCaseTypesForCustomGroupExtends {

  /**
   * Add the class and function for fetching case types for case category.
   */
  public function apply() {
    $description = 'CRM_Civicase_Helper_CaseCategory::getCaseTypesForCase;';

    $result = civicrm_api3('OptionValue', 'getsingle', [
      'option_group_id' => 'cg_extend_objects',
      'label' => CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME,
    ]);

    if (empty($result['id']) || $result['description'] == $description) {
      return;
    }

    civicrm_api3('OptionValue', 'create', [
      'id' => $result['id'],
      'description' => $description,
    ]);
  }

}
