<?php

use CRM_Civicase_Hook_Helper_CaseTypeCategory as CaseTypeCategoryHelper;

/**
 * CRM_Civicase_Hook_BuildForm_DisableCaseCustomFieldValidations class.
 */
class CRM_Civicase_Hook_BuildForm_DisableCaseCustomFieldValidations {

  /**
   * Disables the case custom field form validations.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param string $formName
   *   Form Name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    $caseCategoryName = $this->getCaseCategoryName($form);

    if (!$this->shouldRun($formName, $caseCategoryName)) {
      return;
    }

    if (!CaseTypeCategoryHelper::isValidCategory($caseCategoryName)) {
      return;
    }

    $this->disableCustomFieldValidations($form);
  }

  /**
   * Disables Custom field validations.
   *
   * Disables the validation for case custom fields when a category is present
   * and the category is not of type case.
   *
   * If we don't do this, we will have problems submitting forms when
   * category is not case.
   *
   * @param CRM_Core_Form $form
   *   Form class object.
   */
  private function disableCustomFieldValidations(CRM_Core_Form $form) {
    $result = civicrm_api3('CustomGroup', 'get', [
      'sequential' => 1,
      'extends' => 'Case',
      'api.CustomField.get' => ['custom_group_id' => "\$value.id"],
    ]);

    $customFieldIds = [];
    foreach ($result['values'] as $value) {
      $customFields = !empty($value['api.CustomField.get']['values']) ? $value['api.CustomField.get']['values'] : [];

      foreach ($customFields as $val) {
        $customFieldIds[] = $val['id'];
      }
    }

    $rules = $form->_rules;
    foreach ($customFieldIds as $id) {
      $customFieldElementName = 'custom_' . $id . '_-1';
      if (isset($rules[$customFieldElementName])) {
        unset($rules[$customFieldElementName]);
      }
    }

    $form->_rules = $rules;
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
