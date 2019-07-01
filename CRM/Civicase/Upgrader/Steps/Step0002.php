<?php

class CRM_Civicase_Upgrader_Steps_Step0002 {
  public function apply() {
    $this->addCaseCategoryDBField();
    $this->createCaseCategoryOptionGroup();

    return TRUE;
  }

  /**
   * Add Case Type Category Field to the Case Type table
   */
  private function addCaseCategoryDBField () {
    CRM_Core_DAO::executeQuery('ALTER TABLE civicrm_case_type
    ADD COLUMN case_type_category INT(10)');
  }

  /**
   * Create Case Type Category option group
   */
  private function createCaseCategoryOptionGroup () {
    CRM_Core_BAO_OptionGroup::ensureOptionGroupExists([
      'name' => 'case_type_categories',
      'title' => ts('Case Type Categories'),
      'is_reserved' => 1,
    ]);
  }
}
