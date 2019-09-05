<?php

use CRM_Civicase_Setup_AddCaseTypesForCustomGroupExtends as AddCaseTypesForCustomGroupExtends;

/**
 * CRM_Civicase_Upgrader_Steps_Step004 class.
 */
class CRM_Civicase_Upgrader_Steps_Step0004 {

  /**
   * Add the functionality for fetching case types for case category..
   *
   * @return bool
   *   Return value in boolean.
   */
  public function apply() {
    $step = new AddCaseTypesForCustomGroupExtends();
    $step->apply();

    return TRUE;
  }

}
