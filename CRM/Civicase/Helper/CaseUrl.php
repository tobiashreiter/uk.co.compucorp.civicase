<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;
use CRM_Civicase_Service_CaseCategoryMenu as CaseCategoryMenu;

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
    $categoryName = CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME;
    $submenus = (new CaseCategoryMenu())->getSubmenus(
      CaseCategoryHelper::getDataForMenu()
    );
    $submenus = array_column($submenus, 'url', 'name');

    if ($routeType == 'dashboard') {
      return $submenus["{$categoryName}_dashboard"];
    }
    if ($routeType == 'all') {
      return $submenus["all_{$categoryName}"];
    }
    if ($routeType == 'manage_workflows') {
      return $submenus["manage_{$categoryName}_workflows"];
    }

    return NULL;
  }

  /**
   * Read and return the category Id and Name from URL.
   *
   * @return array
   *   Array with category ID as first element, and category Name as second.
   */
  public static function getCategoryParamsFromUrl() {
    $categoryId = CRM_Utils_Request::retrieve('case_type_category', 'Int');
    if ($categoryId > 0) {
      $categoryName = civicrm_api3('OptionValue', 'getsingle', [
        'option_group_id' => 'case_type_categories',
        'value' => $categoryId,
        'return' => ['name'],
      ])['name'];
    }
    else {
      $categoryName = CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME;
      $categoryId = CaseCategoryHelper::getOptionValue();
    }

    return [$categoryId, $categoryName];
  }

}
