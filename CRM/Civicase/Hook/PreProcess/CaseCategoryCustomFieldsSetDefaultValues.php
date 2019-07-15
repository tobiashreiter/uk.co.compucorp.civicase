<?php

use CRM_Civicase_Hook_Helper_CaseTypeCategory as CaseTypeCategoryHelper;

class CRM_Civicase_Hook_PreProcess_CaseCategoryCustomFieldsSetDefaultValues {

  /**
   * Sets the case category custom fields values in cache so that these values will be set as defaults
   * when reloading the add case form or if the case form fails validation.
   *
   *
   * @param string $formName
   * @param CRM_Core_Form $form
   */
  public function run($formName, &$form) {
    if (!$this->shouldRun($formName, $form)) {
      return;
    }

    $this->setDefaultValuesForCaseCategoryCustomFields($form);
  }

  /**
   * This function processes and sets default values for the case category custom fields. These
   * values are stored in the cache and pre-poluoated on the Add case form particularly for situations
   * where a case is being added and the form fails validation. Without this function the default values
   * set for the Case Category custom fields will not be preserved and user will need to re-enter those
   * values all over again.
   *
   * @param CRM_Core_Form $form
   */
  private function setDefaultValuesForCaseCategoryCustomFields($form) {
    $caseTypeId = CRM_Utils_Array::value('case_type_id', CRM_Utils_Request::exportValues(), $form->_caseTypeId);
    $caseTypeCategoryHelper = new CaseTypeCategoryHelper();
    $caseCategoryId = $caseTypeCategoryHelper->getCategory($caseTypeId);

    //hidden_custom will not be empty when custom field is being edited.
    if (!$caseCategoryId || empty($_POST['hidden_custom'])) {
      return;
    }

    $qfKey = $form->get_template_vars('qfKey');
    $beforeCachedCustomDataValues = CRM_Core_BAO_Cache::getItem('custom data', $qfKey);
    CRM_Custom_Form_CustomData::preProcess($form, NULL, $caseCategoryId, 1, 'CaseCategory');
    $afterCachedCustomDataValues = CRM_Core_BAO_Cache::getItem('custom data', $qfKey);

    //We need to do this because the cached data for the custom values will be overwritten with new values for
    //Case category custom fields If we don't merge both data together.
    if ($beforeCachedCustomDataValues != $afterCachedCustomDataValues) {
      $combinedCachedCustomDataValues = array_merge($beforeCachedCustomDataValues, $afterCachedCustomDataValues);
      CRM_Core_BAO_Cache::setItem($combinedCachedCustomDataValues, 'custom data', $qfKey);
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
