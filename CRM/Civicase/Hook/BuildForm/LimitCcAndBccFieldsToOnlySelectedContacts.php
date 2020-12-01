<?php

/**
 * Limits the cc and bcc fields to selected contacts only for an email popup.
 */
class CRM_Civicase_Hook_BuildForm_LimitCcAndBccFieldsToOnlySelectedContacts {

  /**
   * Limits the cc and bcc fields to selected contacts only for an email popup.
   *
   * @param CRM_Core_Form $form
   *   The current form's instance.
   * @param string $formName
   *   The name for the current form.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    if (!$this->shouldRun($formName)) {
      return;
    }
    $this->limitCcAndBccFields($form);
  }

  /**
   * Limits the cc and bcc fields to selected contacts only for an email popup.
   *
   * @param CRM_Core_Form $form
   *   The current form's instance.
   */
  public function limitCcAndBccFields(CRM_Core_Form &$form) {
    $contactIds = explode(',', CRM_Utils_Array::value('cid', $_GET, '0'));
    foreach (['cc_id' => 'CC', 'bcc_id' => 'BCC'] as $field => $label) {
      $form->removeElement($field);
      $form->addEntityRef($field, ts($label), [
        'entity' => 'Email',
        'multiple' => TRUE,
        'api' => ['params' => ['contact_id' => ['IN' => $contactIds]]],
      ]);
    }
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
  private function shouldRun($formName) {
    return (
      $formName === CRM_Contact_Form_Task_Email::class &&
      CRM_Utils_Array::value('caseRolesBulkEmail', $_GET, '0') === '1' &&
      CRM_Utils_Array::value('snippet', $_GET, '0') === CRM_Core_Smarty::PRINT_JSON &&
      CRM_Utils_Array::value('cid', $_GET, '0') &&
      (bool) Civi::settings()->get('civicaseLimitCcAndBCC')
    );
  }

}
