<?php

/**
 * Add ActivityDateFormat To DateSettings BuildForm Hook Class.
 */
class CRM_Civicase_Hook_BuildForm_AddCaseActivityDateFormatToDateSettings {

  /**
   * Adds Activity DateFormat To Date Settings Form.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param string $formName
   *   Form Name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    if (!$this->shouldRun($formName)) {
      return;
    }

    $this->addActivityDateFormatField($form);
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
   * Add activity date format field.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   */
  private function addActivityDateFormatField($form) {
    $name = 'civiCaseActivityDateformat';
    $fieldName = '_qf_' . $name;
    $field = [
      $fieldName => [
        'html_type' => 'text',
        'title' => ts('Date Format: Activity Feed'),
        'weight' => 5,
      ],
    ];

    $form->add('text', $fieldName, $field[$fieldName]['title'], $field[$fieldName]['attributes']);
    $value = Civi::settings()->get($name) ?? '%d %b %Y';
    $form->setDefaults(array_merge($form->_defaultValues, [$fieldName => $value]));

    CRM_Core_Region::instance('form-body')->add([
      'template' => "CRM/Civicase/Form/CaseActivityDateFormat.tpl",
    ]);
  }

}
