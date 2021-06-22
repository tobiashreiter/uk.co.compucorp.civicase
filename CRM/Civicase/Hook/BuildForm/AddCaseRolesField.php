<?php

/**
 * AddCaseRolesField BuildForm Hook Class.
 */
class CRM_Civicase_Hook_BuildForm_AddCaseRolesField {

  /**
   * Run the hook action.
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

    $form->add(
      'hidden', 'case_contacts_info'
    )->setValue(
      CRM_Utils_Request::retrieve('caseRoles', 'String')
    );

    $templatePath = CRM_Civicase_ExtensionUtil::path() . '/templates';
    CRM_Core_Region::instance('page-body')->add(
      [
        'template' => "{$templatePath}/CRM/Civicase/Form/CaseContactsInfoField.tpl",
      ]
    );
  }

  /**
   * Check whether the hook should run or not.
   *
   * @param string $formName
   *   The name for the current form.
   *
   * @return bool
   *   Whether the hook should run or not.
   */
  private function shouldRun(string $formName) {
    return (
      $formName === CRM_Contact_Form_Task_Email::class
    );
  }

}
