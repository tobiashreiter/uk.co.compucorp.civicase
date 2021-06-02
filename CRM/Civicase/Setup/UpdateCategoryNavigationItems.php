<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;
use CRM_Civicase_Helper_Category as CategoryHelper;

/**
 * Class for updating menus of Case Categories dynamically created.
 *
 * These are marked as "not reserved" ('is_reserved' == 0) and have all the
 * same set of items, according to instance type. *
 */
class CRM_Civicase_Setup_UpdateCategoryNavigationItems {

  /**
   * Update category navigation items.
   */
  public function apply() {
    // Update menu corresponding to not reserved case categories.
    $categories = $this->getNotReservedCategories();

    foreach ($categories as $category) {
      $categoryInstance = CaseCategoryHelper::getInstanceObject($category['value']);
      $categoryMenu = $categoryInstance->getMenuObject();
      $categoryMenu->resetCaseCategorySubmenusUrl(
        CategoryHelper::get($category['name'])
      );
    }

    CRM_Core_BAO_Navigation::resetNavigation();
  }

  /**
   * Get categories with `is_reserved` equal to 0.
   */
  private function getNotReservedCategories() {
    $categories = civicrm_api3('OptionValue', 'get', [
      'option_group_id' => 'case_type_categories',
      'is_reserved' => 0,
    ]);

    if ($categories['count'] === 0) {
      return [];
    }

    return $categories['values'];
  }

}
