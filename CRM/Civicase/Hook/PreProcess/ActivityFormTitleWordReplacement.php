<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;
use CRM_Civicase_Hook_Helper_CaseTypeCategory as CaseCategoryWordReplacementHelper;
use CRM_Utils_System as SystemUtils;
use function ts as civicaseTs;

/**
 * Activity Form Title Word Replacement.
 *
 * Replaces the form title with the right translation for the activity type,
 * depending on the selected activity's case category.
 */
class CRM_Civicase_Hook_PreProcess_ActivityFormTitleWordReplacement {

  /**
   * Runs the activity form title word replacement.
   *
   * @param String $formName
   *   The Form class name.
   * @param Object $form
   *   The Form instance.
   */
  public function run($formName, $form) {
    $caseId = $form->getVar('_caseId');
    $contactId = $form->getVar('_currentlyViewedContactId');

    if (!$this->shouldRun($form)) {
      return;
    }

    $caseCategoryName = CaseCategoryHelper::getCategoryName($caseId);
    CaseCategoryWordReplacementHelper::addWordReplacements($caseCategoryName);
    $translatedActivityTypeName = civicaseTs($form->getVar('_activityTypeName'));

    $formTitle = $contactId
      ? $this->getFormTitleForContactActivity($contactId, $translatedActivityTypeName)
      : ts('%1 Activity', [1 => $translatedActivityTypeName]);

    SystemUtils::setTitle($formTitle);
  }

  /**
   * Returns the form title format for contact activities.
   *
   * This functionality is mirrored from the original Activity Form title.
   *
   * @return String
   *   The contact activity form title.
   */
  private function getFormTitleForContactActivity($contactId, $activityTypeName) {
    $displayName = CRM_Contact_BAO_Contact::displayName($contactId);

    // Checks if this is default domain contact
    if (CRM_Contact_BAO_Contact::checkDomainContact($contactId)) {
      $displayName .= ' (' . ts('default organization') . ')';
    }

    return $displayName . ' - ' . $activityTypeName;
  }

  /**
   * Runs the hook given the following is true:
   *
   * - The form is for activities.
   * - The activity belongs to a case.
   *
   * @param Object $form
   *   The Form instance.
   * @return Bool
   *   True when the form is for case activities.
   */
  private function shouldRun($form) {
    $isActivityForm = get_class($form) === 'CRM_Case_Form_Activity';
    $isCaseActivity = !!$form->getVar('_caseId');

    return $isActivityForm && $isCaseActivity;
  }
}
