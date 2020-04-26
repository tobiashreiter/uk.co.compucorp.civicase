<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;
use CRM_Civicase_Hook_Helper_CaseTypeCategory as CaseCategoryWordReplacementHelper;
use CRM_Core_Session as Session;
use function ts as civicaseTs;

/**
 * Activity Form Status Word Replacement.
 *
 * Replaces the "saved activity type" status message with one appropriate for the
 * case category the activity belongs to.
 */
class CRM_Civicase_Hook_PostProcess_ActivityFormStatusWordReplacement {

  /**
   * Runs the status message word replacement.
   *
   * It clears the original status message, adds the word replacements for the
   * activity's case category, and adds the replaced title. We keep the original
   * status message text.
   *
   * @param String $formName
   *   The Form class name.
   * @param Object $form
   *   The Form instance.
   */
  public function run($formName, $form) {
    $caseId = $form->getVar('_caseId');

    if (!$this->shouldRun($form)) {
      return;
    }

    $caseCategoryName = CaseCategoryHelper::getCategoryName($caseId);
    CaseCategoryWordReplacementHelper::addWordReplacements($caseCategoryName);
    $translatedActivityTypeName = civicaseTs($form->getVar('_activityTypeName'));

    // Gets and resets the status message from the activity form:
    $statusMessages = Session::singleton()->getStatus(true);

    Session::setStatus(
      $statusMessages[0]['text'],
      ts('%1 Saved', [1 => $translatedActivityTypeName]),
      'success'
    );
  }

  /**
   * Allows the hook to run when all of the following is true:
   *
   * - The current form is for an activity.
   * - The activity belongs to a case.
   * - The form is for either creating or updating the activity.
   *
   * @param Object $form
   *   The Form instance.
   * @return Bool
   *   True if the hook class should run.
   */
  private function shouldRun($form) {
    $isActivityForm = get_class($form) === 'CRM_Case_Form_Activity';
    $isCaseActivity = !!$form->getVar('_caseId');
    $isCreateOrUpdateAction = $form->getVar('_action') === CRM_Core_Action::ADD ||
      $form->getVar('_action') === CRM_Core_Action::UPDATE;

    return $isActivityForm && $isCaseActivity && $isCreateOrUpdateAction;
  }
}
