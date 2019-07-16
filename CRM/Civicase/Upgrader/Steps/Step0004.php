<?php

use CRM_Civicase_Setup_AddCaseCategoryCgExtendsValue as AddCaseCategoryCgExtendsValue;

class CRM_Civicase_Upgrader_Steps_Step0004 {

  /**
   * Add the CaseCategory as a valid Entity that a custom group can extend.
   *
   * @return bool
   */
  public function apply() {
    $step = new AddCaseCategoryCgExtendsValue();
    $step->apply();

    return TRUE;
  }
}
