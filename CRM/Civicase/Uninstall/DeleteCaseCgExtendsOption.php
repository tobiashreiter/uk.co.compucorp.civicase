<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * Class CRM_Civicase_Uninstall_DeleteCaseCgExtendsOption.
 */
class CRM_Civicase_Uninstall_DeleteCaseCgExtendsOption {

  /**
   * Deletes the Cases option from the CG Extends option values.
   */
  public function apply() {
    $result = civicrm_api3('OptionValue', 'get', [
      'sequential' => 1,
      'value' => CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME,
      'option_group_id' => 'cg_extend_objects',
    ]);

    if ($result['count'] == 0) {
      return;
    }

    CRM_Core_BAO_OptionValue::del($result['values'][0]['id']);
  }

}
