<?php

use CRM_Civicase_Service_CaseCategoryMenu as CaseCategoryMenu;

/**
 * Class CRM_Civicase_Service_CaseManagementUtils.
 */
class CRM_Civicase_Service_CaseManagementUtils extends CRM_Civicase_Service_CaseCategoryInstanceUtils {

  /**
   * Returns the menu object for the default category instance.
   *
   * @return \CRM_Civicase_Service_CaseCategoryMenu
   *   Menu object.
   */
  public function getMenuObject() {
    return new CaseCategoryMenu();
  }

}
