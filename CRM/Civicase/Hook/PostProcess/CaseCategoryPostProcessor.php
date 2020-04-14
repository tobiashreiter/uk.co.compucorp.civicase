<?php

use CRM_Civicase_Factory_CaseTypeCategoryEventHandler as CaseTypeCategoryEventHandlerFactory;

/**
 * Class CRM_Civicase_Hook_PostProcess_CaseCategoryPostProcessor.
 */
class CRM_Civicase_Hook_PostProcess_CaseCategoryPostProcessor {

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

    // Get object data from submitted from.
    $formValues = $form->_submitValues;
    $caseCategoryValues = $form->getVar('_values');
    $category = [
      'id' => $form->getVar('_id'),
      'name' => !empty($caseCategoryValues['name']) ? $caseCategoryValues['name'] : $formValues['label'],
      'is_active' => $formValues['is_active'],
      'icon' => $formValues['icon'],
    ];

    $op = $form->getVar('_action');
    $handler = CaseTypeCategoryEventHandlerFactory::create();

    if ($op == CRM_Core_Action::UPDATE) {
      $handler->onUpdate($category['id'], $category['is_active'], $category['icon']);
    }
    elseif ($op == CRM_Core_Action::ADD) {
      $handler->onCreate($category['name']);
    }
    elseif ($op == CRM_Core_Action::DELETE) {
      $handler->onDelete($category['name']);
    }
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
