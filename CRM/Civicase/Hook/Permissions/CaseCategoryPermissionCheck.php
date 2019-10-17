<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;
use CRM_Civicase_Service_CaseCategoryPermission as CaseCategoryPermission;
use CRM_Case_BAO_CaseType as CaseType;

/**
 * Class CRM_Civicase_Hook_Permissions_CaseCategoryPermissionCheck
 */
class CRM_Civicase_Hook_Permissions_CaseCategoryPermissionCheck {

  /**
   * @var CRM_Civicase_Service_CaseCategoryPermission
   */
  private $caseCategoryPermission;

  /**
   * @var bool
   */
  private $isCaseEntity;

  /**
   * Modify permission check based on case category.
   *
   * @param string $permission
   *   Permission name.
   * @param bool $granted
   *   Whether permission is granted or not.
   */
  public function run($permission, &$granted) {
    $this->caseCategoryPermission = new CaseCategoryPermission();

    if (!$this->shouldRun($permission, $granted)) {
      return;
    }

    $url = rtrim(CRM_Utils_Request::retrieve('q', 'String'), '/');
    $isAjaxRequest = $url == 'civicrm/ajax/rest';
    $caseCategoryName = $this->getCaseCategoryName();

    if ($caseCategoryName) {
      $this->modifyPermissionCheckForCategory($permission, $granted, $caseCategoryName);
    }
    elseif ($isAjaxRequest && !$this->isCaseEntity) {
      $this->modifyPermissionCheckForAjaxRequest($permission, $granted);
    }

  }

  /**
   * Modify permission check for Ajax requests when not case entity.
   *
   * When an AJAX request is not of the case entity, for example calls to
   * Activity.get will still check the type of activity and the component it
   * belongs to thereby invoking Civicase permission for activity types for the
   * civicase component. To fix such issues, we check that the user has at least
   * any of the equivalent civicase permissions.
   *
   * @param array $permission
   * @param bool $granted
   */
  private function modifyPermissionCheckForAjaxRequest($permission, &$granted) {
    $caseTypeCategories = CaseType::buildOptions('case_type_category', 'validate');
    foreach ($caseTypeCategories as $caseTypeCategory) {
      if ($caseTypeCategory == CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME) {
        continue;
      }
      $caseCategoryPermission = $this->caseCategoryPermission->replaceWords($permission, $caseTypeCategory);
      if (!CRM_Core_Permission::check($caseCategoryPermission)) {
        $granted = FALSE;
      }
      else {
        $granted = TRUE;
        break;
      }
    }
  }

  /**
   * Modify permission check based on case category.
   *
   * @param string $permission
   *   Permission name.
   * @param bool $granted
   *   Whether permission is granted or not.
   * @param string $caseCategoryName
   *   Case category name.
   */
  private function modifyPermissionCheckForCategory($permission, &$granted, $caseCategoryName) {
    if ($caseCategoryName && strtolower($caseCategoryName) == strtolower(CaseCategoryHelper::CASE_TYPE_CATEGORY_NAME)) {
      return;
    }

    $caseCategoryPermission = $this->caseCategoryPermission->replaceWords($permission, $caseCategoryName);

    if (!CRM_Core_Permission::check($caseCategoryPermission)) {
      $granted = FALSE;
    }
    else {
      $granted = TRUE;
    }
  }

  /**
   * Determines if the hook will run.
   *
   * @param string $permission
   *   Permission name.
   * @param bool $granted
   *   Whether permission is granted or not.
   *
   * @return bool
   *   returns a boolean to determine if hook will run or not.
   */
  private function shouldRun($permission, $granted) {
    $defaultCasePermissions = array_column($this->caseCategoryPermission->get(), 'name');

    return in_array($permission, $defaultCasePermissions) && !$granted;
  }

  /**
   * Gets the case type category name if it exists.
   *
   * @param bool $isViewCase
   *   If the page is for View case.
   *
   * @return string|null
   *   The case category name.
   */
  private function getCaseCategoryName() {
    $url = rtrim(CRM_Utils_Request::retrieve('q', 'String'), '/');
    $isViewCase = $url == 'civicrm/contact/view/case';
    $isCasePage = ($url == 'civicrm/case/add' || $url == 'civicrm/case/a');
    $isAjaxRequest = $url == 'civicrm/ajax/rest';

    if ($isViewCase) {
      return $this->getCaseCategoryForViewCase();
    }

    if ($isAjaxRequest) {
      return $this->getCaseCategoryForAjaxRequest();
    }

    if ($isCasePage) {
      return $this->getCaseCategoryFromUrl();
    }
  }

  /**
   * Gets the case category type from the URL.
   *
   * @return mixed|null
   */
  private function getCaseCategoryFromUrl() {
    $caseCategoryName = CRM_Utils_Request::retrieve('case_type_category', 'String');

    if ($caseCategoryName) {
      return $caseCategoryName;
    }

    $entryURL = CRM_Utils_Request::retrieve('entryURL', 'String');

    $urlParams = parse_url(htmlspecialchars_decode($entryURL), PHP_URL_QUERY);
    parse_str($urlParams, $urlParams);

    if (!empty($urlParams['case_type_category'])) {
      return $urlParams['case_type_category'];
    }

    return NULL;
  }


  /**
   * Get case category name for view case.
   *
   * The view case page is the page civi redirects to after
   * adding a new case. It does not have the case type category
   * parameter in the URL since it's an internal civi page but we
   * can use the just created Case Id to get the case type category.
   */
  private function getCaseCategoryForViewCase() {
    $caseId = CRM_Utils_Request::retrieve('id', 'Integer');

    return CaseCategoryHelper::getCategoryName($caseId);
  }

  /**
   * Get case category name for Ajax case API requests.
   */
  private function getCaseCategoryForAjaxRequest() {
    $entity = CRM_Utils_Request::retrieve('entity', 'String');
    $json = CRM_Utils_Request::retrieve('json', 'String');
    $json = $json ? json_decode($json, TRUE) : [];

    if ($entity && strtolower($entity) == 'case') {
      $this->isCaseEntity = TRUE;
      if (isset($json['case_type_id.case_type_category'])) {
        return $this->getCaseTypeCategoryNameFromOptions($json['case_type_id.case_type_category']);
      }
    }

    if (strtolower($entity) == 'api3') {
      foreach ($json as $entity) {
        list($entityName, $action, $params) = $entity;

        if (strtolower($entityName) == 'case') {
          $this->isCaseEntity = TRUE;
          if (isset($params['case_type_id.case_type_category'])) {
            return $this->getCaseTypeCategoryNameFromOptions($params['case_type_id.case_type_category']);
          }
        }
      }
    }
  }

  /**
   * Returns the case category name from case type id or name.
   *
   * @param mixed $caseTypeCategory
   *   Case category name.
   *
   * @return string
   *   Case category name.
   */
  private function getCaseTypeCategoryNameFromOptions($caseTypeCategory) {
    if (!is_numeric($caseTypeCategory)) {
      return $caseTypeCategory;
    }

    return CaseCategoryHelper::getCaseCategoryNameFromOptionValue($caseTypeCategory);
  }
}
