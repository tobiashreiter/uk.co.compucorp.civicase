<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;
use CRM_Case_BAO_CaseType as CaseType;

/**
 * Class MoveCaseTypesToCasesCategory setup.
 */
class CRM_Civicase_Setup_MoveCaseTypesToCasesCategory {

  /**
   * Moves Case Types with no category to category "Cases".
   */
  public function apply() {
    $this->moveCaseTypesWithNoCategoryToCasesCategory();
  }

  /**
   * Moves Case Types with no category to category "Cases".
   */
  private function moveCaseTypesWithNoCategoryToCasesCategory() {
    $caseTypeTable = CaseType::getTableName();
    $caseTypes = CRM_Core_DAO::executeQuery("SELECT id FROM {$caseTypeTable} WHERE case_type_category IS NULL");
    $caseTypeIds = [];

    while ($caseTypes->fetch()) {
      $caseTypeIds[] = $caseTypes->id;
    }

    if (empty($caseTypeIds)) {
      return;
    }
    $this->updateCaseTypeCategory($caseTypeIds);
  }

  /**
   * Updates the case type category to "Cases".
   *
   * @param array $caseTypeId
   *   Case Type Id.
   */
  private function updateCaseTypeCategory(array $caseTypeId) {
    $caseTypeTable = CaseType::getTableName();
    $caseCategoryOptionValue = CaseCategoryHelper::getOptionValue();

    CRM_Core_DAO::executeQuery(
      "UPDATE {$caseTypeTable} SET case_type_category = %1 WHERE id IN (" . implode(',', $caseTypeId) . ")",
      [1 => [$caseCategoryOptionValue, 'Integer']]
    );
  }

}
