<?php

/**
 * CRM_Civicase_Setup_AddProspectCategoryCgExtendsValue class.
 */
class CRM_Civicase_Setup_AddProspectCategoryCgExtendsValue {

  /**
   * Add the CaseCategory as a valid Entity that a custom group can extend.
   */
  public function apply() {
    $prospectCategoryLabel = 'prospecting';

    CRM_Core_BAO_OptionValue::ensureOptionValueExists([
      'option_group_id' => 'cg_extend_objects',
      'name' => $prospectCategoryLabel,
      'label' => 'Case (Prospects)',
      'value' => $prospectCategoryLabel,
      'is_active' => TRUE,
      'is_reserved' => TRUE,
    ]);
  }

}
