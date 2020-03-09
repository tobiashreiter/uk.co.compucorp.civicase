<?php

use CRM_Civicase_Service_CaseCategorySetting as CaseCategorySetting;

/**
 * Class CRM_Civicase_Hook_PreProcess_AddCaseAdminSettings.
 */
class CRM_Civicase_Hook_PreProcess_AddCaseAdminSettings {

  /**
   * Sets the case admin settings.
   *
   * @param string $formName
   *   Form name.
   * @param CRM_Core_Form $form
   *   Form object class.
   */
  public function run($formName, CRM_Core_Form &$form) {
    if (!$this->shouldRun($formName)) {
      return;
    }

    $settings = $form->getVar('_settings');
    $settings['civicaseAllowCaseLocks'] = CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME;
    $this->setCaseCategoryWebformSettings($form, $settings);

    $form->setVar('_settings', $settings);
  }

  /**
   * Sets webform settings for case categories.
   *
   * @param CRM_Core_Form $form
   *   Form object class.
   * @param array $settings
   *   Settings array.
   */
  private function setCaseCategoryWebformSettings(CRM_Core_Form &$form, array &$settings) {
    $caseSetting = new CaseCategorySetting();
    $caseCategoryWebFormSetting = $caseSetting->getForWebform();
    $settingKeys = array_keys($caseCategoryWebFormSetting);

    foreach ($settingKeys as $settingKey) {
      $settings[$settingKey] = CRM_Core_BAO_Setting::SYSTEM_PREFERENCES_NAME;
    }

    $form->assign('caseCategoryWebFormSetting', $caseCategoryWebFormSetting);
  }

  /**
   * Determines if the hook will run.
   *
   * @param string $formName
   *   Form class object.
   *
   * @return bool
   *   returns TRUE or FALSE.
   */
  private function shouldRun($formName) {
    return $formName == 'CRM_Admin_Form_Setting_Case';
  }

}
