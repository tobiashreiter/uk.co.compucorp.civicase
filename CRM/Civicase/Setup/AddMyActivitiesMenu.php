<?php

/**
 * Adds My Activities Menu item.
 */
class CRM_Civicase_Setup_AddMyActivitiesMenu {

  /**
   * My Activities URL.
   */
  const MY_ACTIVITIES_URL = '/civicrm/case/my-activities';

  /**
   * Adds My Activities Menu item.
   */
  public function apply() {
    $this->addMenuLink();
  }

  /**
   * Adds My Activities Menu item.
   */
  private function addMenuLink() {
    $userMenu = civicrm_api3('Navigation', 'get', [
      'sequential' => 1,
      'name' => 'user-menu-ext__user-menu',
    ])['values'];

    if (empty($userMenu)) {
      return;
    }

    $myAccountMenu = civicrm_api3('Navigation', 'get', [
      'sequential' => 1,
      'name' => 'user-menu-ext__user-menu__my-account',
    ])['values'][0];

    $myActivitiesMenu = civicrm_api3('Navigation', 'create', [
      'is_active' => 1,
      'parent_id' => 'user-menu-ext__user-menu',
      'permission' => 'access CiviCRM',
      'label' => 'My Activities',
      'name' => 'civicase__my-activities__menu',
      'url' => self::MY_ACTIVITIES_URL,
      'icon' => 'fa fa-check',
    ]);

    // Menu weight seems to be ignored on create irrespective of whatever is
    // passed, Civi will assign the next available weight. This fixes the issue.
    civicrm_api3('Navigation', 'create', [
      'id' => $myActivitiesMenu['id'],
      'weight' => $myAccountMenu['weight'] - 1,
    ]);
  }

}
