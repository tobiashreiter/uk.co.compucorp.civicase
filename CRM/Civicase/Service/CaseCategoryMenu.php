<?php

use CRM_Civicase_Service_CaseCategoryPermission as CaseCategoryPermission;

/**
 * Create/Delete Case Type Category Menu items.
 */
class CRM_Civicase_Service_CaseCategoryMenu {

  /**
   * Creates Case Category Main menu and sub menus.
   *
   * @param string $caseTypeCategoryName
   *   Case Type category name.
   */
  public function createItems($caseTypeCategoryName) {
    $result = civicrm_api3('Navigation', 'get', ['name' => $caseTypeCategoryName]);

    if ($result['count'] > 0) {
      return;
    }

    $caseCategoryPermission = new CaseCategoryPermission();
    $permissions = $caseCategoryPermission->get($caseTypeCategoryName);
    $casesWeight = CRM_Core_DAO::getFieldValue(
      'CRM_Core_DAO_Navigation',
      'Cases',
      'weight',
      'name'
    );

    $params = [
      'label' => ts($caseTypeCategoryName),
      'name' => $caseTypeCategoryName,
      'url' => NULL,
      'permission_operator' => 'OR',
      'is_active' => 1,
      'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
      'icon' => 'crm-i fa-folder-open-o',
    ];

    $caseCategoryMenu = civicrm_api3('Navigation', 'create', $params);
    // Menu weight seems to be ignored on create irrespective of whatever is
    // passed, Civi will assign the next available weight. This fixes the issue.
    civicrm_api3('Navigation', 'create', [
      'id' => $caseCategoryMenu['id'],
      'weight' => $casesWeight + 1,
    ]);
    $this->createCaseCategorySubmenus($caseTypeCategoryName, $permissions, $caseCategoryMenu['id']);
  }

  /**
   * Creates the Case Category Sub Menus.
   *
   * @param string $caseTypeCategoryName
   *   Case category name.
   * @param array $permissions
   *   Permissions.
   * @param int $caseCategoryMenuId
   *   Menu ID.
   */
  private function createCaseCategorySubmenus($caseTypeCategoryName, array $permissions, $caseCategoryMenuId) {
    $submenus = [
      [
        'label' => ts('Dashboard'),
        'name' => "{$caseTypeCategoryName}_dashboard",
        'url' => "civicrm/case/a/?case_type_category={$caseTypeCategoryName}#/case?case_type_category={$caseTypeCategoryName}",
        'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
        'has_separator' => 1,
      ],
      [
        'label' => ts("New {$caseTypeCategoryName}"),
        'name' => "new_{$caseTypeCategoryName}",
        'url' => "civicrm/case/add?case_type_category={$caseTypeCategoryName}&action=add&reset=1&context=standalone",
        'permission' => "{$permissions['ADD_CASE_CATEGORY']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
      ],
      [
        'label' => ts("My {$caseTypeCategoryName}"),
        'name' => "my_{$caseTypeCategoryName}",
        'url' => '/civicrm/case/a/?case_type_category=' . $caseTypeCategoryName . '#/case/list?cf={"case_type_category":"' . $caseTypeCategoryName . '","case_manager":"user_contact_id"}',
        'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
      ],
      [
        'label' => ts("My {$caseTypeCategoryName} activities"),
        'name' => "my_activities_{$caseTypeCategoryName}",
        'url' => '/civicrm/case/a/?case_type_category=' . $caseTypeCategoryName . '#/case?case_type_category=' . $caseTypeCategoryName . '&dtab=1&af={"case_filter":{"case_type_id.is_active":1,"contact_is_deleted":0,"case_type_id.case_type_category":"' . $caseTypeCategoryName . '"},
        "@involvingContact":"myActivities"}&drel=all',
        'permission' => 'access CiviCRM',
        'has_separator' => 1,
      ],
      [
        'label' => ts("All {$caseTypeCategoryName}"),
        'name' => "all_{$caseTypeCategoryName}",
        'url' => 'civicrm/case/a/?case_type_category=' . $caseTypeCategoryName . '#/case/list?cf={"case_type_category":"' . $caseTypeCategoryName . '"}',
        'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
      ],
    ];

    foreach ($submenus as $i => $item) {
      $item['weight'] = $i;
      $item['parent_id'] = $caseCategoryMenuId;
      $item['is_active'] = 1;
      civicrm_api3('Navigation', 'create', $item);
    }
  }

  /**
   * Deletes the Case Category Main menu and sub menus.
   *
   * @param string $caseTypeCategoryName
   *   Case category name.
   */
  public function deleteItems($caseTypeCategoryName) {
    $parentMenu = civicrm_api3('Navigation', 'get', ['name' => $caseTypeCategoryName]);

    if ($parentMenu['count'] == 0) {
      return;
    }

    $result = civicrm_api3('Navigation', 'get', ['parent_id' => $parentMenu['id']]);
    foreach ($result['values'] as $submenu) {
      civicrm_api3('Navigation', 'delete', ['id' => $submenu['id']]);
    }

    civicrm_api3('Navigation', 'delete', ['id' => $parentMenu['id']]);
  }

  /**
   * Disables/Enables the Case Category Main menu.
   *
   * @param string $caseTypeCategoryName
   *   Case category name.
   * @param bool $status
   *   Status.
   */
  public function toggleStatus($caseTypeCategoryName, $status) {
    $parentMenu = civicrm_api3('Navigation', 'get', ['name' => $caseTypeCategoryName]);

    if ($parentMenu['count'] == 0) {
      return;
    }

    civicrm_api3('Navigation', 'create', ['id' => $parentMenu['id'], 'is_active' => $status ? 1 : 0]);
  }

}
