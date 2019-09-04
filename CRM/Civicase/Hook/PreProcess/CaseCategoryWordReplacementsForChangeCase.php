<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * Class CaseCategoryWordReplacementsForChangeCase.
 */
class CRM_Civicase_Hook_PreProcess_CaseCategoryWordReplacementsForChangeCase {

  /**
   * Adds the word replacements array to Civi's translation locale.
   *
   * @param string $formName
   *   Form Name.
   * @param CRM_Core_Form $form
   *   Form Class object.
   */
  public function run($formName, CRM_Core_Form &$form) {
    if (!$this->shouldRun($formName, $form)) {
      return;
    }

    $this->addWordReplacements($form);
  }

  /**
   * Adds the word replacements array to Civi's translation locale.
   *
   * This will make Civi automatically translate form labels that are
   * displayed using the ts function.
   *
   * @param CRM_Core_Form $form
   *   Page class.
   */
  private function addWordReplacements(CRM_Core_Form $form) {
    $caseCategoryName = CaseCategoryHelper::getCategoryName($form->_caseId[0]);
    CRM_Civicase_Hook_Helper_CaseTypeCategory::addWordReplacements($caseCategoryName);
    // We need to translate this manually as Civi does not the page title
    // through the ts function.
    $pageTitle = $form->get_template_vars('activityTypeName');
    CRM_Utils_System::setTitle(ts($pageTitle));
  }

  /**
   * Determines if the hook will run.
   *
   * @param string $formName
   *   Form name.
   * @param CRM_Core_Form $form
   *   Form class object.
   *
   * @return bool
   *   returns a boolean to determine if hook will run or not.
   */
  private function shouldRun($formName, CRM_Core_Form $form) {
    if ($formName != 'CRM_Case_Form_Activity') {
      return FALSE;
    }

    return $form->_activityTypeName == 'Change Case Type';
  }

}
