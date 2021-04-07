<?php

use CRM_Civicase_Helper_CaseUrl as CaseUrlHelper;

/**
 * Class for updating menus corresponding to Cases.
 *
 * This is the base Case category, and the menus that require update are
 * Manage Cases ('all' route) and Manage Workflows ('manage_workflows' route)
 */
class CRM_Civicase_Setup_UpdateCasesNavigationItems {

  /**
   * Update Cases items.
   */
  public function apply() {
    $itemData = [
      'Manage Cases' => 'all',
      'manage_Cases_workflows' => 'manage_workflows',
    ];

    foreach ($itemData as $itemName => $itemRoute) {
      civicrm_api3('Navigation', 'get', [
        'parent_id' => CRM_Civicase_Helper_CaseCategory::CASE_TYPE_CATEGORY_NAME,
        'name' => $itemName,
        'options' => ['limit' => 1],
        'api.Navigation.create' => [
          'id' => '$value.id',
          'url' => CaseUrlHelper::getUrlByRouteType($itemRoute),
        ],
      ]);
    }
  }

}
