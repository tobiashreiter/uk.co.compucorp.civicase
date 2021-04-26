<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategory;

/**
 * Class for updating menus corresponding to Cases.
 */
class CRM_Civicase_Setup_UpdateCasesNavigationItems {

  /**
   * Update Cases items.
   */
  public function apply() {
    $casesId = CaseCategory::getOptionValue();

    $casesNavigationId = civicrm_api3('Navigation', 'getsingle', [
      'name' => CaseCategory::CASE_TYPE_CATEGORY_NAME,
      'return' => 'id',
    ])['id'];

    $navigationItems = civicrm_api3('Navigation', 'get', [
      'parent_id' => $casesNavigationId,
      'options' => ['limit' => 0],
    ])['values'];

    foreach ($navigationItems as $item) {
      civicrm_api3('Navigation', 'get', [
        'id' => $item['id'],
        'api.Navigation.create' => [
          'id' => '$value.id',
          'url' => str_ireplace(CaseCategory::CASE_TYPE_CATEGORY_NAME, $casesId, $item['url']),
        ],
      ]);
    }
  }

}
