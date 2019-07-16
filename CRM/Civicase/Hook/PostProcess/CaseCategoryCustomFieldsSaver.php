<?php

class CRM_Civicase_Hook_PostProcess_CaseCategoryCustomFieldsSaver {

  /**
   * Saves/Processes the Case category custom field values for the Case.
   *
   * @param string $formName
   * @param CRM_Core_Form $form
   */
  public function run($formName, &$form) {
    if (!$this->shouldRun($formName)) {
      return;
    }

    $this->processCaseCategoryCustomFieldValues($form);
  }

  /**
   * Caves the case category custom field values for the case.
   *
   * @param CRM_Core_Form $form
   */
  private function processCaseCategoryCustomFieldValues($form) {
    $values = $form->_submitValues;
    $customFieldValues = CRM_Core_BAO_CustomField::postProcess(
      $values,
      NULL,
      'CaseCategory'
    );

    $caseTableName = CRM_Case_BAO_Case::getTableName();
    if ($customFieldValues) {
      CRM_Core_BAO_CustomValueTable::store($customFieldValues, $caseTableName, $form->_caseId);
    }
  }

  /**
   * Determines if the hook will run.
   *
   * @param string $formName
   *
   * @return bool
   */
  private function shouldRun($formName) {
    return $formName == CRM_Case_Form_Case::class;
  }
}
