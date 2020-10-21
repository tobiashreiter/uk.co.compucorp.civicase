<?php

use CRM_Civicase_Setup_AddManageWorkflowMenu as AddManageWorkflowMenu;

/**
 * Assigns instance to case type categories without an instance.
 *
 * Also creates Manage Workflow menu for existing 'case management' type
 * categories.
 */
class CRM_Civicase_Upgrader_Steps_Step0014 {

  /**
   * Runs the upgrader changes.
   *
   * @return bool
   *   Return value in boolean.
   */
  public function apply() {
    $step = new AddManageWorkflowMenu();
    $step->apply();

    return TRUE;
  }

}
