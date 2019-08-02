<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * CRM_Civicase_Hook_PostProcess_ProcessCaseCategoryCustomFieldsForSave class.
 */
class CRM_Civicase_Hook_PostProcess_ProcessCaseCategoryCustomFieldsForSave {

  /**
   * Processes/Displays the case category custom fields.
   *
   * The case category custom fields on Manage Case category page will not work
   * well without this, basically it allows the the custom field values for
   * the case category to be saved/updated.
   *
   * @param string $formName
   *   Form name.
   * @param CRM_Core_Form $form
   *   Form object class.
   */
  public function run($formName, CRM_Core_Form &$form) {
    if (!$this->shouldRun($formName)) {
      return;
    }

    $caseId = CRM_Utils_Request::retrieve('entityID', 'Positive', $form, TRUE);
    $caseCategoryName = CaseCategoryHelper::getCategoryName($caseId);

    if ($caseCategoryName == 'Cases') {
      return;
    }

    $this->saveCaseCategoryCustomFieldValues($form, $caseCategoryName);
  }

  /**
   * Processes the Case Category custom fields values.
   *
   * Allows them to be saved for case categories other than 'Case'.
   *
   * @param CRM_Core_Form $form
   *   Form name.
   * @param string $caseCategoryName
   *   Case Category name.
   */
  private function saveCaseCategoryCustomFieldValues(CRM_Core_Form $form, $caseCategoryName) {
    $params = $form->controller->exportValues();
    $caseId = CRM_Utils_Request::retrieve('entityID', 'Positive', $form, TRUE);

    CRM_Core_BAO_CustomValueTable::postProcess($params,
      'civicrm_case',
      $caseId,
      $caseCategoryName
    );
  }

  /**
   * Determines if the hook will run.
   *
   * @param string $formName
   *   Form name.
   *
   * @return bool
   *   returns TRUE or FALSE.
   */
  private function shouldRun($formName) {
    return $formName == CRM_Case_Form_CustomData::class;
  }

}
