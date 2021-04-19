<?php

use CRM_Civicase_Service_CaseCategoryCustomFieldsSetting as CaseCategoryCustomFieldsSetting;

/**
 * SaveCaseCategoryCustomFields Post Process Hook class.
 */
class CRM_Civicase_Hook_PostProcess_SaveCaseCategoryCustomFields extends CRM_Civicase_Hook_CaseCategoryFormHookBase {

  /**
   * Saves the case category custom field values.
   *
   * @param string $formName
   *   The Form class name.
   * @param CRM_Core_Form $form
   *   The Form instance.
   */
  public function run($formName, CRM_Core_Form $form) {
    if (!$this->shouldRun($form, $formName)) {
      return;
    }

    $caseCategoryCustomFields = new CaseCategoryCustomFieldsSetting();
    $caseCategoryValues = $form->getVar('_submitValues');

    $caseCategoryCustomFields->save($caseCategoryValues['value'], [
      'singular_label' => $caseCategoryValues['singular_label'],
    ]);
  }

}
