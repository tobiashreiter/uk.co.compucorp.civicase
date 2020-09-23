<?php

/**
 * Base class for the CaseCategoryInstance hook classes.
 */
class CRM_Civicase_Hook_CaseCategoryInstanceBase {

  /**
   * Instance field name.
   */
  const INSTANCE_TYPE_FIELD_NAME = 'case_category_instance_type';

  /**
   * Determines the condition for the hook to run.
   *
   * The hook will run when the option value for the case type category
   * option group is being created or edited.
   *
   * @param CRM_Core_Form $form
   *   Form object class.
   * @param string $formName
   *   Form name.
   *
   * @return bool
   *   True if the hook class should run.
   */
  protected function shouldRun(CRM_Core_Form $form, $formName) {
    $formAction = $form->getVar('_action');
    $optionGroupName = $form->getVar('_gName');
    return $formName == CRM_Admin_Form_Options::class
      && $optionGroupName == 'case_type_categories' && $formAction != CRM_Core_Action::DELETE;
  }

}
