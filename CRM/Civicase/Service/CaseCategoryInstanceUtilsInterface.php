<?php

/**
 * Interface CRM_Civicase_Service_CaseCategoryInstanceUtilsInterface.
 */
interface CRM_Civicase_Service_CaseCategoryInstanceUtilsInterface {

  /**
   * Returns the menu object for the category instance.
   *
   * @return CRM_Civicase_Service_CaseCategoryMenu
   *   Menu object.
   */
  public function getMenuObject();

}
