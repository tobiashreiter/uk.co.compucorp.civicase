<?php

use CRM_Civicase_Service_CaseCategoryMenu as CaseCategoryMenuService;

/**
 * Class CRM_Civicase_Hook_PostProcess_CaseCategoryMenuLinksProcessor.
 */
class CRM_Civicase_Hook_PostProcess_CaseCategoryMenuLinksProcessor {

  /**
   * Case Category Menu Links Processor.
   *
   * Creates/Deletes menus for the Case category option group is saved/deleted
   * based on the form action.
   *
   * @param string $formName
   *   Form name.
   * @param CRM_Core_Form $form
   *   Form object class.
   */
  public function run($formName, CRM_Core_Form &$form) {
    if (!$this->shouldRun($form, $formName)) {
      return;
    }

    $caseCategoryMenu = new CaseCategoryMenuService();
    $formValues = $form->_submitValues;
    $formAction = $form->getVar('_action');
    if ($formAction == CRM_Core_Action::DELETE) {
      $caseCategoryValues = $form->getVar('_values');
      $caseCategoryMenu->deleteItems($caseCategoryValues['name']);
    }

    if ($formAction == CRM_Core_Action::ADD) {
      $caseCategoryMenu->createItems($formValues['label']);
    }

    if ($formAction == CRM_Core_Action::UPDATE) {
      $updateParams = [
        'is_active' => !empty($formValues['is_active']) ? 1 : 0,
        'icon' => 'crm-i ' . $formValues['icon'],
      ];

      $caseCategoryMenu->updateItems($form->getVar('_id'), $updateParams);
    }
  }

  /**
   * Returns the option value name given it's id.
   *
   * @param int $id
   *   Option value id.
   *
   * @return string
   *   Option value name.
   */
  private function getOptionValueName($id) {
    $result = civicrm_api3('OptionValue', 'get', [
      'option_group_id' => 'case_type_categories',
      'id' => $id,
      'return' => ['name'],
    ]);

    return $result['values'][$id]['name'];
  }

  /**
   * Determines if the hook will run.
   *
   * @param CRM_Core_Form $form
   *   Form object class.
   * @param string $formName
   *   Form name.
   *
   * @return bool
   *   returns TRUE or FALSE.
   */
  private function shouldRun(CRM_Core_Form $form, $formName) {
    $optionGroupName = $form->getVar('_gName');
    return $formName == 'CRM_Admin_Form_Options' && $optionGroupName == 'case_type_categories';
  }

}
