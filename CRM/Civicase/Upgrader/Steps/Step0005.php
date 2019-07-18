<?php

use CRM_Civicase_Setup_AddProspectCategoryCgExtendsValue as AddProspectCategoryCgExtendsValue;

/**
 * CRM_Civicase_Upgrader_Steps_Step0005 class.
 */
class CRM_Civicase_Upgrader_Steps_Step0005 {

  /**
   * Add Prospect Case Category as a valid Entity that a custom group extends.
   *
   * @return bool
   *   Returns a boolean.
   */
  public function apply() {
    $step = new AddProspectCategoryCgExtendsValue();
    $step->apply();

    return TRUE;
  }

}
