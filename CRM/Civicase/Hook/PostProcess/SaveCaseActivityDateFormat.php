<?php

/**
 * Save Activity DateFormat.
 */
class CRM_Civicase_Hook_PostProcess_SaveCaseActivityDateFormat {

  /**
   * Saves The Activity DateFormat field.
   *
   * @param string $formName
   *   Form Name.
   * @param CRM_Core_Form $form
   *   Form Class object.
   */
  public function run($formName, CRM_Core_Form $form) {
    if (!$this->shouldRun($formName)) {
      return;
    }

    $this->saveCaseActivityDateFormatField($form);
  }

  /**
   * Checks if this shook should run.
   *
   * @param string $formName
   *   Form Name.
   *
   * @return bool
   *   True if the hook should run.
   */
  public function shouldRun($formName) {
    return $formName == CRM_Admin_Form_Setting_Date::class;
  }

  /**
   * Saves The Activity DateFormat field.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   */
  public function saveCaseActivityDateFormatField(CRM_Core_Form &$form) {
    $values = $form->getVar('_submitValues');
    if (!empty($values['_qf_civiCaseActivityDateformat'])) {
      Civi::settings()->set('civiCaseActivityDateformat', $values['_qf_civiCaseActivityDateformat']);
    }
  }

}
