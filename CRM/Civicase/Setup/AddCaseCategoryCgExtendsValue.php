<?php

/**
 * CRM_Civicase_Setup_AddCaseCategoryCgExtendsValue class.
 */
class CRM_Civicase_Setup_AddCaseCategoryCgExtendsValue {

  /**
   * Add the CaseCategory as a valid Entity that a custom group can extend.
   */
  public function apply() {
    $caseCategoryLabel = 'CaseCategory';

    CRM_Core_BAO_OptionValue::ensureOptionValueExists([
      'option_group_id' => 'cg_extend_objects',
      'name' => 'civicrm_case_type',
      'label' => $caseCategoryLabel,
      'value' => $caseCategoryLabel,
      'is_active' => TRUE,
      'is_reserved' => TRUE,
    ]);
  }

}
