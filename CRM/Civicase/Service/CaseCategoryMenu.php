<?php

use CRM_Civicase_Service_CaseCategoryPermission as CaseCategoryPermission;
use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

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
    $labelForMenu = ucfirst(strtolower($caseTypeCategoryName));

    $result = civicrm_api3('Navigation', 'get', ['name' => $caseTypeCategoryName]);

    if ($result['count'] > 0) {
      return;
    }

    $caseCategoryPermission = new CaseCategoryPermission();
    $permissions = $caseCategoryPermission->get($caseTypeCategoryName);

    $params = [
      'label' => ts($labelForMenu),
      'name' => $caseTypeCategoryName,
      'url' => NULL,
      'permission_operator' => 'OR',
      'is_active' => 1,
      'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
      'icon' => $this->getMenuIconByCategoryName($caseTypeCategoryName),
    ];

    $caseCategoryMenu = civicrm_api3('Navigation', 'create', $params);
    // Menu weight seems to be ignored on create irrespective of whatever is
    // passed, Civi will assign the next available weight. This fixes the issue.
    civicrm_api3('Navigation', 'create', [
      'id' => $caseCategoryMenu['id'],
      'weight' => CaseCategoryHelper::getWeightOfCasesMenu() + 1,
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
  protected function createCaseCategorySubmenus($caseTypeCategoryName, array $permissions, $caseCategoryMenuId) {
    $submenus = $this->getSubmenus($caseTypeCategoryName, $permissions);

    foreach ($submenus as $i => $item) {
      $item['weight'] = $i;
      $item['parent_id'] = $caseCategoryMenuId;
      $item['is_active'] = 1;
      civicrm_api3('Navigation', 'create', $item);
    }
  }

  /**
   * Reset the submenus of the given category.
   *
   * @param string $caseTypeCategoryName
   *   Case type category name.
   */
  public function resetCaseCategorySubmenusUrl(string $caseTypeCategoryName) {
    $submenus = $this->getSubmenus($caseTypeCategoryName);

    foreach ($submenus as $item) {
      civicrm_api3('Navigation', 'get', [
        'name' => $item['name'],
        'api.Navigation.create' => [
          'id' => '$value.id',
          'url' => $item['url'],
        ],
      ]);
    }
  }

  /**
   * Creates the Case Category Sub Menus.
   *
   * @param string $caseTypeCategoryName
   *   Case category name.
   * @param array|null $permissions
   *   Permissions.
   *
   * @return array[]
   *   Array with the submenus info.
   */
  public function getSubmenus($caseTypeCategoryName, array $permissions = NULL) {
    $labelForMenu = ucfirst(strtolower($caseTypeCategoryName));
    $categoryId = civicrm_api3('OptionValue', 'getsingle', [
      'option_group_id' => 'case_type_categories',
      'name' => $caseTypeCategoryName,
      'return' => ['value'],
    ])['value'];
    if (!$permissions) {
      $permissions = (new CaseCategoryPermission())->get($caseTypeCategoryName);
    }

    return [
      [
        'label' => ts('Dashboard'),
        'name' => "{$caseTypeCategoryName}_dashboard",
        'url' => "civicrm/case/a/?case_type_category={$categoryId}&p=dd#/case?case_type_category={$categoryId}",
        'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
        'has_separator' => 1,
      ],
      [
        'label' => ts("New {$labelForMenu}"),
        'name' => "new_{$caseTypeCategoryName}",
        'url' => "civicrm/case/add?case_type_category={$categoryId}&p=ad&action=add&reset=1&context=standalone",
        'permission' => "{$permissions['ADD_CASE_CATEGORY']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
      ],
      [
        'label' => ts("My {$labelForMenu}"),
        'name' => "my_{$caseTypeCategoryName}",
        'url' => '/civicrm/case/a/?case_type_category=' . $categoryId . '&p=my#/case/list?cf={"case_type_category":"' . $categoryId . '","case_manager":"user_contact_id"}',
        'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
      ],
      [
        'label' => ts("My {$labelForMenu} activities"),
        'name' => "my_activities_{$caseTypeCategoryName}",
        'url' => '/civicrm/case/a/?case_type_category=' . $categoryId . '&p=ma#/case?case_type_category=' . $categoryId . '&dtab=1&af={"case_filter":{"case_type_id.is_active":1,"contact_is_deleted":0,"case_type_id.case_type_category":"' . $categoryId . '"},
        "@involvingContact":"myActivities"}&drel=all',
        'permission' => 'access CiviCRM',
        'has_separator' => 1,
      ],
      [
        'label' => ts("All {$labelForMenu}"),
        'name' => "all_{$caseTypeCategoryName}",
        'url' => 'civicrm/case/a/?case_type_category=' . $categoryId . '&p=mg#/case/list?cf={"case_type_category":"' . $categoryId . '"}',
        'permission' => "{$permissions['ACCESS_MY_CASE_CATEGORY_AND_ACTIVITIES']['name']},{$permissions['ACCESS_CASE_CATEGORY_AND_ACTIVITIES']['name']}",
        'permission_operator' => 'OR',
        'has_separator' => 1,
      ],
      [
        'label' => ts("Manage Workflows"),
        'name' => "manage_{$caseTypeCategoryName}_workflows",
        'url' => 'civicrm/workflow/a?case_type_category=' . $categoryId . '&p=al#/list',
        'permission' => "{$permissions['ADMINISTER_CASE_CATEGORY']['name']}, administer CiviCRM",
        'permission_operator' => 'OR',
      ],
    ];
  }

  /**
   * Get menu icon name by category name.
   *
   * @param string $categoryName
   *   Case Type Category name.
   *
   * @return string
   *   The css class for icon to be used on the menu.
   */
  private function getMenuIconByCategoryName(string $categoryName) {
    $optionValueDetails = CaseCategoryHelper::getByName($categoryName);

    if (!empty($optionValueDetails['icon'])) {
      return "crm-i " . $optionValueDetails['icon'];
    }

    return 'crm-i fa-folder-open-o';
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
   * @param int $caseCategoryId
   *   Case category name.
   * @param array $menuParams
   *   Case category name.
   */
  public function updateItems($caseCategoryId, array $menuParams) {
    $caseCategoryOptionDetails = CaseCategoryHelper::getById($caseCategoryId);

    $parentMenu = civicrm_api3('Navigation', 'get', ['name' => $caseCategoryOptionDetails['name']]);

    if ($parentMenu['count'] == 0) {
      return;
    }

    $menuParams['id'] = $parentMenu['id'];
    civicrm_api3('Navigation', 'create', $menuParams);
  }

}
