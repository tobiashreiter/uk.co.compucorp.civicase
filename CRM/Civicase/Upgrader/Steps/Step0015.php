<?php

use CRM_Civicase_Setup_UpdateCasesNavigationItems as UpdateCasesItems;
use CRM_Civicase_Setup_UpdateCategoryNavigationItems as UpdateCategoryItems;

/**
 * Update menus with new URL.
 */
class CRM_Civicase_Upgrader_Steps_Step0015 {

  /**
   * Runs the upgrader changes.
   *
   * @return bool
   *   Return value in boolean.
   */
  public function apply() {
    (new UpdateCasesItems())->apply();
    (new UpdateCategoryItems())->apply();

    return TRUE;
  }

}
