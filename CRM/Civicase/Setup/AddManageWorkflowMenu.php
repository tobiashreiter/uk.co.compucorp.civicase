<?php

use CRM_Civicase_Service_ManageWorkflowMenu as ManageWorkflowMenu;

/**
 * Adds the Manage Workflow Menu item for existing Case types.
 */
class CRM_Civicase_Setup_AddManageWorkflowMenu {

  /**
   * Updates the Manage Cases Menu URLs.
   */
  public function apply() {
    (new ManageWorkflowMenu())->create('case_management', FALSE);
  }

}
