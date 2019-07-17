<?php

/**
 * CRM_Civicase_Hook_BuildForm_FilterCaseTypeByCategory class.
 */
class CRM_Civicase_Hook_BuildForm_FilterCaseTypeByCategory {

  /**
   * Filters the options for the case type select element based on the category.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param string $formName
   *   Form Name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    $caseCategoryName = CRM_Utils_Request::retrieve('category', 'String');
    if (!$this->shouldRun($formName, $caseCategoryName)) {
      return;
    }

    $this->filterCaseTypeOptionValues($form, $caseCategoryName);
  }

  /**
   * Filters the options for the case type select element based on the category.
   *
   * @param CRM_Core_Form $form
   *   Form class object.
   * @param string $caseCategoryName
   *   Case category name.
   */
  private function filterCaseTypeOptionValues(CRM_Core_Form $form, $caseCategoryName) {
    $caseTypesInCategory = $this->getCaseTypesForCategory($caseCategoryName);

    if (!$caseTypesInCategory) {
      return;
    }

    $caseTypeIdElement = &$form->getElement('case_type_id');
    $options = $caseTypeIdElement->_options;

    foreach ($options as $key => $option) {
      $optionValue = $option['attr']['value'];
      if (!in_array($optionValue, $caseTypesInCategory) && $optionValue) {
        unset($options[$key]);
      }
    }

    $caseTypeIdElement->_options = $options;
  }

  /**
   * Returns the case type ids for a case type category.
   *
   * @param string $caseCategoryName
   *   Case category name.
   *
   * @return array|null
   *   The case type id's e.g [1, 2, 3]
   */
  private function getCaseTypesForCategory($caseCategoryName) {
    try {
      $result = civicrm_api3('CaseType', 'get', [
        'return' => ['id'],
        'case_type_category' => $caseCategoryName,
      ]);

      if ($result['count'] == 0) {
        return NULL;
      }

      return array_column($result['values'], 'id');
    }
    catch (Exception $e) {
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
    $isCaseForm = $formName == CRM_Case_Form_Case::class;

    return $isCaseForm && $caseCategoryName;
  }

}
