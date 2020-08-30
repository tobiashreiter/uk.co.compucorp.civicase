<?php

use CRM_Civicase_Service_CaseCategorySetting as CaseCategorySetting;
use CRM_Civicase_Hook_Helper_CaseTypeCategory as CaseTypeCategoryHelper;

/**
 * Fetches and redirects user to web form url for current case type category.
 */
class CRM_Civicase_Hook_BuildForm_CaseTypeCategoryWebFormRedirect {

  /**
   * Case category Setting.
   *
   * @var CRM_Civicase_Service_CaseCategorySetting
   *   CaseCategorySetting service.
   */
  private $caseCategorySetting;

  /**
   * Initialize dependencies.
   */
  public function __construct() {
    $this->caseCategorySetting = new CaseCategorySetting();
  }

  /**
   * Fetches and redirects user to web form url for current case type category.
   *
   * @param CRM_Core_Form $form
   *   Form object.
   * @param string $formName
   *   Form name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    if (!$this->shouldRun($formName)) {
      return;
    }
    $this->redirectToWebForm();
  }

  /**
   * Checks the form name and snippet parameter.
   *
   * @param string $formName
   *   Form name.
   *
   * @return bool
   *   Whether this hook should run or not.
   */
  private function shouldRun($formName) {
    return (
      $formName === CRM_Case_Form_Case::class &&
      CRM_Utils_Array::value('snippet', $_GET, '') !== 'json'
    );
  }

  /**
   * Redirect to web form if available for current case type category.
   */
  private function redirectToWebForm() {
    $caseTypeCategoryName = CRM_Utils_Array::value('case_type_category', $_GET, 'Cases');
    $webFormUrl = CaseTypeCategoryHelper::getNewCaseCategoryWebformUrl($caseTypeCategoryName, $this->caseCategorySetting);
    if ($webFormUrl) {
      CRM_Utils_System::redirect(CRM_Utils_System::url(trim($webFormUrl, '/'), ['reset' => 1], FALSE));
    }
  }

}
