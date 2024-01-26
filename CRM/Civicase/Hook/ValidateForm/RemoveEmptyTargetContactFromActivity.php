<?php

/**
 * Remove empty target contact from activity.
 *
 * CiviCRM errors out if an empty string is sent for target
 * contact id and to solve this we remove the target_contact_id param
 * for activity form if its value is empty string.
 */
class CRM_Civicase_Hook_ValidateForm_RemoveEmptyTargetContactFromActivity {

  /**
   * Implement the remove empty target contact functionality.
   *
   * @param string $formName
   *   Form Name.
   * @param array $fields
   *   Fields List.
   * @param array $files
   *   Files list.
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param array $errors
   *   Errors.
   *
   * @return bool
   *   TRUE if the hook ran, false otherwise.
   */
  public function run(
    string $formName,
    array &$fields,
    array &$files,
    CRM_Core_Form $form,
    array &$errors
  ): bool {
    if (!$this->shouldRun($formName)) {
      return FALSE;
    }

    $submittedData = &$form->controller->container();
    if (is_array($submittedData)
      && isset($submittedData['values']['Activity']['target_contact_id'])
      && $submittedData['values']['Activity']['target_contact_id'] === ''
    ) {
      unset($submittedData['values']['Activity']['target_contact_id']);
    }

    return TRUE;
  }

  /**
   * Determines if the hook will run.
   *
   * @param string $formName
   *   Form Name.
   *
   * @return bool
   *   Returns TRUE the Hook should run.
   */
  private function shouldRun(string $formName): bool {
    return $formName === 'CRM_Case_Form_Activity';
  }

}
