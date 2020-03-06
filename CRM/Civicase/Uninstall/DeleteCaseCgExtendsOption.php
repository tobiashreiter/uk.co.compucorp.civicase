<?php

use CRM_Civicase_Setup_AddCaseCategoryCgExtendsValue as AddCaseCategoryCgExtendsValue;

/**
 * Class CRM_CiviAwards_Setup_DeleteCaseCgExtendsOptionValue.
 */
class CRM_CiviAwards_Setup_DeleteCaseCgExtendsOption {

  /**
   * Deletes the Awards option from the CG Extends option values.
   */
  public function apply() {
    $result = civicrm_api3('OptionValue', 'get', [
      'sequential' => 1,
      'label' => AddCaseCategoryCgExtendsValue::CASE_CATEGORY_LABEL,
      'option_group_id' => 'cg_extend_objects',
    ]);

    if ($result['count'] == 0) {
      return;
    }

    CRM_Core_BAO_OptionValue::del($result['values'][0]['id']);
  }

}
