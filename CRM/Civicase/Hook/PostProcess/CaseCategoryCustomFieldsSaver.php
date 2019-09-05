<?php

/**
 * CRM_Civicase_Hook_PostProcess_CaseCategoryCustomFieldsSaver class.
 */
class CRM_Civicase_Hook_PostProcess_CaseCategoryCustomFieldsSaver {

  /**
   * Saves/Processes the Case category custom field values for the Case.
   *
   * @param string $formName
   *   Form name.
   * @param CRM_Core_Form $form
   *   Form object class.
   */
  public function run($formName, CRM_Core_Form &$form) {
    $caseCategoryName = $this->getCaseCategoryName($form);
    if (!$this->shouldRun($formName, $caseCategoryName)) {
      return;
    }

    $this->processCaseCategoryCustomFieldValues($form, $caseCategoryName);
  }

  /**
   * Caves the case category custom field values for the case.
   *
   * @param CRM_Core_Form $form
   *   Form object class.
   * @param string $caseCategoryName
   *   Category Name.
   */
  private function processCaseCategoryCustomFieldValues(CRM_Core_Form $form, $caseCategoryName) {
    if (!$caseCategoryName) {
      return;
    }

    $values = $form->_submitValues;
    $customFieldValues = CRM_Core_BAO_CustomField::postProcess(
      $values,
      NULL,
      $caseCategoryName
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
   *   Form name.
   * @param string $caseCategoryName
   *   Case category name.
   *
   * @return bool
   *   returns a boolean to determine if hook will run or not.
   */
  private function shouldRun($formName, $caseCategoryName) {
    if (!$caseCategoryName) {
      return FALSE;
    }

    $isCaseForm = $formName == CRM_Case_Form_Case::class;
    $caseCategoryNameNotCase = $caseCategoryName != 'cases';

    return $isCaseForm && $caseCategoryName && $caseCategoryNameNotCase;
  }

  /**
   * Gets the Case Category Name under consideration.
   *
   * @param CRM_Core_Form $form
   *   Form name.
   *
   * @return string|null
   *   case category name.
   */
  private function getCaseCategoryName(CRM_Core_Form $form) {
    $urlParams = parse_url(htmlspecialchars_decode($form->controller->_entryURL), PHP_URL_QUERY);
    parse_str($urlParams, $urlParams);

    return !empty($urlParams['case_type_category']) ? $urlParams['case_type_category'] : NULL;
  }

}
