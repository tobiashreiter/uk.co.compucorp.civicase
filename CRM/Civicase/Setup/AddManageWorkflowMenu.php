<?php

use CRM_Civicase_Service_CaseCategoryMenu as CaseCategoryMenu;
use CRM_Civicase_Service_CaseInstance as CaseInstance;

/**
 * Adds the Manage Workflow Menu item for existing Case types.
 */
class CRM_Civicase_Setup_AddManageWorkflowMenu {

  /**
   * Updates the Manage Cases Menu URLs.
   */
  public function apply() {
    CaseInstance::assignInstanceForExistingCaseCategories();
    CaseCategoryMenu::createManageWorkflowMenuForExistingCaseCategories('case_management', FALSE);
  }

}
