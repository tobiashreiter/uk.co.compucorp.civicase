<?php

/**
 * Class CaseCategoryFormLabelTranslationForChangeCase.
 */
class CRM_Civicase_Hook_BuildForm_CaseCategoryFormLabelTranslationForChangeCase {

  /**
   * Translate some case form labels that Civi did not run translation for.
   *
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param string $formName
   *   Form Name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    if (!$this->shouldRun($formName, $form)) {
      return;
    }

    $this->translateFormLabels($form);
  }

  /**
   * Translate some case form labels that Civi did not run translation for.
   *
   * Some Form labels are not ran through Civ's Ts function. We need to
   * do this, so this function does that.
   *
   * @param CRM_Core_Form $form
   *   Page class.
   */
  private function translateFormLabels(CRM_Core_Form $form) {
    $caseTypeIdElement = &$form->getElement('case_type_id');
    $this->translateLabel([$caseTypeIdElement]);
  }

  /**
   * Translate the form labels for an array of form elements.
   *
   * @param array $elements
   *   For Elements array.
   */
  private function translateLabel(array $elements) {
    foreach ($elements as $element) {
      $label = ts($element->getLabel());
      $element->setLabel($label);
    }
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
