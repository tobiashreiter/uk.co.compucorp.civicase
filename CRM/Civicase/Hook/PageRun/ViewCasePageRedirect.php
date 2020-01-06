<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * Class CRM_Civicase_Hook_PageRun_ViewCasePageRedirect.
 */
class CRM_Civicase_Hook_PageRun_ViewCasePageRedirect {

  /**
   * Redirects the core view case page to an angular page provided by civicase.
   *
   * @param object $page
   *   Page Object.
   */
  public function run(&$page) {
    $caseId = CRM_Utils_Request::retrieve('id', 'Positive');

    if (!$this->shouldRun($page, $caseId)) {
      return;
    }

    $this->redirectViewCasePage($caseId);
  }

  /**
   * Redirects the core view case page to an angular page provided by civicase.
   *
   * @param int $caseId
   *   Case Id.
   */
  private function redirectViewCasePage($caseId) {
    // OLD: http://localhost/civicrm/contact/view/case?reset=1&action=view&cid=129&id=51
    // NEW: http://localhost/civicrm/case/a/?case_type_category=case_category_name#/case/list?
    // sf=contact_id.sort_name&sd=ASC&focus=0&cf=%7B%7D&caseId=51&tab=summary&sx=0
    $case = civicrm_api3('Case', 'getsingle', [
      'id' => $caseId,
      'return' => [
        'case_type_id.name',
        'status_id',
        'case_type_id.case_type_category',
      ],
    ]);

    $caseCategoryName = CaseCategoryHelper::getCaseCategoryNameFromOptionValue($case['case_type_id.case_type_category']);
    $url = CRM_Utils_System::url('civicrm/case/a/', ['case_type_category' => strtolower($caseCategoryName)], TRUE,
      "/case/list?sf=id&sd=DESC&caseId={$caseId}&cf=%7B%22case_type_category%22:%22" . strtolower($caseCategoryName) . "%22,%22status_id%22:%5B%22{$case['status_id']}%22%5D,%22case_type_id%22:%5B%22{$case['case_type_id.name']}%22%5D%7D",
      FALSE);

    CRM_Utils_System::redirect($url);
  }

  /**
   * Determines if the hook will run.
   *
   * @param object $page
   *   Page Object.
   * @param int $caseId
   *   Case Id.
   *
   * @return bool
   *   returns a boolean to determine if hook will run or not.
   */
  private function shouldRun($page, $caseId) {
    return $page instanceof CRM_Case_Page_Tab && $caseId;
  }

}
