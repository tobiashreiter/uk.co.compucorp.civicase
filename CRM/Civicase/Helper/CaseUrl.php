<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

/**
 * Case URL Helper.
 *
 * Returns URLs for case pages.
 */
class CRM_Civicase_Helper_CaseUrl {

  /**
   * Returns the URL for the case details page.
   *
   * @param int $caseId
   *   The ID of the case we want the details URL for.
   *
   * @return string
   *   The case details URL.
   */
  public static function getDetailsPage($caseId) {
    $case = civicrm_api3('Case', 'getsingle', [
      'id' => $caseId,
      'return' => [
        'case_type_id.name',
        'case_type_id.case_type_category',
        'status_id',
      ],
    ]);
    $caseFilters = [
      'case_type_id' => [$case['case_type_id.name']],
      'status_id' => [$case['status_id']],
    ];
    $caseDetailsUrlPath = sprintf(
      'civicrm/case/a/?case_type_category=%d#/case/list?caseId=%d&sf=id&sd=DESC&cf=%s',
      $case['case_type_id.case_type_category'],
      $caseId,
      urlencode(json_encode($caseFilters))
    );

    return CRM_Utils_System::url($caseDetailsUrlPath, NULL, TRUE);
  }

  /**
   * Get the URL of the route type specified, for Cases category.
   *
   * @return string
   *   Url to be returned.
   */
  public static function getUrlByRouteType(string $routeType) {
    $categoryId = CaseCategoryHelper::getOptionValue();

    if ($routeType == 'dashboard') {
      return "civicrm/case/a/?p=dd#/case?case_type_category={$categoryId}";
    }
    if ($routeType == 'all') {
      return 'civicrm/case/a/?p=mg#/case/list?cf={"case_type_category":"' . $categoryId . '"}';
    }
    if ($routeType == 'manage_workflows') {
      return 'civicrm/workflow/a?case_type_category=' . $categoryId . '&p=al#/list';
    }

    return NULL;
  }

}
