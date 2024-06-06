<?php

use CRM_Civicase_ExtensionUtil as E;

/**
 * FormatCaseActivityDateFormat BuildForm Hook Class.
 */
class CRM_Civicase_Hook_BuildForm_FormatCaseActivityDateFormat {

  /**
   * Adds Activity DateFormat To Date Settings Form.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param string $formName
   *   Form Name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    if (!$this->shouldRun($form, $formName)) {
      return;
    }

    $format = Civi::settings()->get('civiCaseActivityDateformat') ?? '%d %b %Y';
    $dateValue = CRM_Utils_Date::customFormat($form->_defaultValues['activity_date_time'], $format);

    \Civi::resources()->addVars('civicase', ['formatted_date' => $dateValue]);
    \Civi::resources()->add([
      'scriptFile' => [E::LONG_NAME, 'js/modify-activity-date.js'],
      'region' => 'form-body',
    ]);
  }

  /**
   * Checks if this shook should run.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param string $formName
   *   Form Name.
   *
   * @return bool
   *   True if the hook should run.
   */
  public function shouldRun($form, $formName) {
    return $formName == CRM_Activity_Form_Activity::class && $form->getAction() & CRM_Core_Action::VIEW;
  }

}
