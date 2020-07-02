<?php

/**
 * Interface CRM_Civicase_Service_CaseCategoryInstanceUtilsInterface.
 */
abstract class CRM_Civicase_Service_CaseCategoryInstanceUtils {

  /**
   * Primary key holding relationship between the instance and case category.
   *
   * @var int
   */
  private $caseCategoryInstanceKey;

  /**
   * Returns the menu object for the category instance.
   *
   * @return CRM_Civicase_Service_CaseCategoryMenu
   *   Menu object.
   */
  abstract public function getMenuObject();

  /**
   * CRM_Civicase_Service_CaseCategoryInstanceUtils constructor.
   *
   * @param int|null $caseCategoryInstanceKey
   *   Case Category Instance Key.
   */
  public function __construct($caseCategoryInstanceKey = NULL) {
    $this->caseCategoryInstanceKey = $caseCategoryInstanceKey;
  }

  /**
   * Returns the caseCategoryInstanceKey variable.
   *
   * @return int
   *   Case category instance key.
   */
  public function getCaseCategoryInstanceKey() {
    return $this->caseCategoryInstanceKey;
  }

}
