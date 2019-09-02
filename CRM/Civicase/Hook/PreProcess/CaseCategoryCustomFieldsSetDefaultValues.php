<?php

/**
 * CRM_Civicase_Hook_PreProcess_CaseCategoryCustomFieldsSetDefaultValues class.
 */
class CRM_Civicase_Hook_PreProcess_CaseCategoryCustomFieldsSetDefaultValues {

  /**
   * Sets the case category custom fields values in cache to be set as defaults.
   *
   * When reloading the add case form or if the case form fails validation.
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

    $this->setDefaultValuesForCaseCategoryCustomFields($form, $caseCategoryName);
  }

  /**
   * Sets default values for Case category custom fields.
   *
   * This function processes, set default values for the category custom fields,
   * these values are stored in cache and pre-populated on the add case form.
   *
   * When a case is being added and the form fails validation.
   * Without this function the default values set for the Case Category
   * custom fields will not be preserved and user will need to
   * re-enter those values all over again.
   *
   * @param CRM_Core_Form $form
   *   Form class object.
   * @param string $caseCategoryName
   *   Case Category name.
   */
  private function setDefaultValuesForCaseCategoryCustomFields(CRM_Core_Form $form, $caseCategoryName) {
    $caseTypeId = CRM_Utils_Array::value('case_type_id', CRM_Utils_Request::exportValues(), $form->_caseTypeId);

    // hidden_custom will not be empty when custom field is being edited.
    if (empty($_POST['hidden_custom'])) {
      return;
    }

    $qfKey = $form->get_template_vars('qfKey');
    $beforeCachedCustomDataValues = Civi::cache('customData')->get($qfKey);
    CRM_Custom_Form_CustomData::preProcess($form, NULL, $caseTypeId, 1, $caseCategoryName);
    CRM_Custom_Form_CustomData::buildQuickForm($form);
    CRM_Custom_Form_CustomData::setDefaultValues($form);
    $afterCachedCustomDataValues = Civi::cache('customData')->get($qfKey);
    $this->unsetCaseCategoryCustomGroupFromGroupTree($form, $caseCategoryName);

    // We need to do this because the cached data for the custom values will be
    // overwritten with new values for Case category custom fields If
    // we don't merge both data together.
    if ($beforeCachedCustomDataValues && $beforeCachedCustomDataValues != $afterCachedCustomDataValues) {
      $combinedCachedCustomDataValues = array_merge($beforeCachedCustomDataValues, $afterCachedCustomDataValues);
      Civi::cache('customData')->set($qfKey, $combinedCachedCustomDataValues);
    }
  }

  /**
   * Determines if the hook will run.
   *
   * @param string $formName
   *   Form class object.
   * @param string $caseCategoryName
   *   Case Category name.
   *
   * @return bool
   *   returns TRUE or FALSE.
   */
  private function shouldRun($formName, $caseCategoryName) {
    if (!$caseCategoryName) {
      return FALSE;
    }

    $isCaseForm = $formName == CRM_Case_Form_Case::class;
    $caseCategoryNameNotCase = $caseCategoryName != 'case';

    return $isCaseForm && $caseCategoryName && $caseCategoryNameNotCase;
  }

  /**
   * Gets the Category Name for the case.
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

  /**
   * Removes the case category custom groups from the group tree.
   *
   * Without this the form elements will be displayed twice in UI,
   * the Case Form Class does this logic also.
   *
   * @param CRM_Core_Form $form
   *   Form name.
   * @param string $caseCategoryName
   *   Case Category name.
   */
  private function unsetCaseCategoryCustomGroupFromGroupTree(CRM_Core_Form $form, $caseCategoryName) {
    $result = civicrm_api3('CustomGroup', 'get', [
      'sequential' => 1,
      'extends' => $caseCategoryName,
    ]);

    if ($result['count'] == 0) {
      return;
    }

    foreach ($result['values'] as $value) {
      unset($form->_groupTree[$value['id']]);
    }
  }

}
