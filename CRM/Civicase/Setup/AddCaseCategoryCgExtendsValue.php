<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * CRM_Civicase_Setup_AddCaseCategoryCgExtendsValue class.
 */
class CRM_Civicase_Setup_AddCaseCategoryCgExtendsValue {

  const CASE_CATEGORY_LABEL = 'Case (Cases)';

  /**
   * Add Cases as a valid Entity that a custom group can extend.
   */
  public function apply() {
    $result = civicrm_api3('OptionValue', 'get', [
      'option_group_id' => 'cg_extend_objects',
      'value' => CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME,
    ]);

    if ($result['count'] > 0) {
      return;
    }

    civicrm_api3('OptionValue', 'create', [
      'option_group_id' => 'cg_extend_objects',
      'name' => 'civicrm_case',
      'label' => self::CASE_CATEGORY_LABEL,
      'value' => CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME,
      'description' => NULL,
      'is_active' => TRUE,
      'is_reserved' => TRUE,
    ]);
  }
}
