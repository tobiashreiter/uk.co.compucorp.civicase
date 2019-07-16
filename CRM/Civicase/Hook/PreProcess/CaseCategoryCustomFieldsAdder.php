<?php

use CRM_Civicase_Hook_Helper_CaseTypeCategory as CaseTypeCategoryHelper;

class CRM_Civicase_Hook_PreProcess_CaseCategoryCustomFieldsAdder {

  /**
   * Adds the Case category custom field values for the Case Type
   *
   * @param string $formName
   * @param CRM_Core_Form $form
   */
  public function run($formName, &$form) {
    if (!$this->shouldRun($formName, $form)) {
      return;
    }

    $this->addCaseCategoryCustomFields($form);
  }

  /**
   * Determines if the hook will run. Will run if the form is the custom data type
   * form and of type case. The form is responsible for adding custom fields for
   * a case type.
   *
   * @param string $formName
   * @param CRM_Core_Form $form
   *
   * @return bool
   */
  private function shouldRun($formName, &$form) {
    return $formName == CRM_Custom_Form_CustomDataByType::class && $form->_type == 'Case';
  }

  /**
   * Adds the custom fields for the case category of the case to the form.
   *
   * @param CRM_Core_Form $form
   */
  private function addCaseCategoryCustomFields(&$form) {
    $form->_type = 'CaseCategory';
    $caseTypeCategoryHelper = new CaseTypeCategoryHelper();
    $caseCategoryId = $caseTypeCategoryHelper->getCategory($form->_subType);
    CRM_Custom_Form_CustomData::setGroupTree($form, $caseCategoryId, $form->_groupID, $form->_onlySubtype);
    $form->_type = 'Case';
  }
}
