<?php

use CRM_Civicase_Setup_AddSingularLabels as AddSingularLabels;

/**
 * Adds singular labels to case type categories.
 */
class CRM_Civicase_Upgrader_Steps_Step0016 {

  /**
   * Runs the upgrader changes.
   *
   * @return bool
   *   True when the upgrader runs successfully.
   */
  public function apply() {
    (new AddSingularLabels())->apply();

    return TRUE;
  }

}
