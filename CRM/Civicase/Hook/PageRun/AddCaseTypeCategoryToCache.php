<?php

use CRM_Civicase_Helper_CaseUrl as CaseUrlHelper;

/**
 * Add case type category to cache.
 */
class CRM_Civicase_Hook_PageRun_AddCaseTypeCategoryToCache {

  /**
   * Add case type category to cache.
   *
   * @param object $page
   *   Page Object.
   */
  public function run(&$page): void {
    $this->addCaseTypeCategoryToCache();
  }

  /**
   * Add case type category to cache.
   */
  private function addCaseTypeCategoryToCache(): void {
    if (CRM_Utils_System::currentPath() === 'civicrm/case/a') {
      $caseCategoryInfo = CaseUrlHelper::getCategoryParamsFromUrl();
      \Civi::cache('metadata')->set('current_case_category', $caseCategoryInfo[1]);
    }
    elseif (!in_array(CRM_Utils_System::currentPath(), ['civicrm/asset/builder', 'civicrm/user-menu'], TRUE)) {
      \Civi::cache('metadata')->set('current_case_category', NULL);
    }
  }

}
