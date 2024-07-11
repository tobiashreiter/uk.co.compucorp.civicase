<?php

use Civi\Api4\OptionGroup;
use CRM_Civicase_Helper_CaseUrl as CaseUrlHelper;
use CRM_Civicase_Service_CaseCategorySetting as CaseCategorySetting;

/**
 * Class CRM_Civicase_Hook_Navigation_AlterForCaseMenu.
 */
class CRM_Civicase_Hook_NavigationMenu_AlterForCaseMenu extends CRM_Civicase_Hook_NavigationMenu_AbstractMenuAlter {

  /**
   * Case category Setting.
   *
   * @var CRM_Civicase_Service_CaseCategorySetting
   *   CaseCategorySetting service.
   */
  private $caseCategorySetting;

  /**
   * Modifies the navigation menu.
   *
   * Modifies the menu so that some memnu URL's can be changed
   * or some menu's dynamically inserted.
   *
   * @param array $menu
   *   Menu Array.
   */
  public function run(array &$menu) {
    $this->caseCategorySetting = new CaseCategorySetting();
    $this->rewriteCaseUrls($menu);
    $this->addCaseWebformUrl($menu);
    $this->addCiviCaseInstanceMenu($menu);
  }

  /**
   * Rewrite some case menu URL's.
   *
   * @param array $menu
   *   Menu Array.
   */
  private function rewriteCaseUrls(array &$menu) {
    // Array(string $oldUrl => string $newUrl).
    $rewriteMap = [
      'civicrm/case?reset=1' => CaseUrlHelper::getUrlByRouteType('dashboard'),
      'civicrm/case/search?reset=1' => 'civicrm/case/a/#/case/list?sx=1&p=fn',
    ];

    $this->menuWalk($menu, function (&$item) use ($rewriteMap) {
      if (!isset($item['url'])) {
        return;
      }

      if (isset($rewriteMap[$item['url']])) {
        $item['url'] = $rewriteMap[$item['url']];

        return;
      }
    });
  }

  /**
   * Adds the civicase Webform menu to the Adminsiter Civicase Menu.
   *
   * @param array $menu
   *   Menu Array.
   */
  private function addCaseWebformUrl(array &$menu) {
    // Add new menu item
    // Check that our item doesn't already exist.
    $menu_item_search = ['url' => 'civicrm/case/webforms'];
    $menu_items = [];
    CRM_Core_BAO_Navigation::retrieve($menu_item_search, $menu_items);

    if (!empty($menu_items)) {
      return;
    }

    $navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
    if (is_int($navId)) {
      $navId++;
    }
    // Find the Civicase menu.
    $caseID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'CiviCase', 'id', 'name');
    $administerID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Administer', 'id', 'name');
    $menu[$administerID]['child'][$caseID]['child'][$navId] = [
      'attributes' => [
        'label' => ts('CiviCase Webforms'),
        'name' => 'CiviCase Webforms',
        'url' => 'civicrm/case/webforms',
        'permission' => 'access all cases and activities',
        'operator' => 'OR',
        'separator' => 1,
        'parentID' => $caseID,
        'navID' => $navId,
        'active' => 1,
      ],
    ];
  }

  /**
   * Visit every link in the navigation menu, and alter it using $callback.
   *
   * @param array $menu
   *   Tree of menu items, per hook_civicrm_navigationMenu.
   * @param callable $callback
   *   Function(&$item).
   */
  private function menuWalk(array &$menu, callable $callback) {
    foreach (array_keys($menu) as $key) {
      if (isset($menu[$key]['attributes'])) {
        $callback($menu[$key]['attributes']);
      }

      if (isset($menu[$key]['child'])) {
        $this->menuWalk($menu[$key]['child'], $callback);
      }
    }
  }

  /**
   * Adds the civicase instance menu to the Adminsiter Civicase Menu.
   *
   * @param array $menu
   *   Tree of menu items, per hook_civicrm_navigationMenu.
   */
  private function addCiviCaseInstanceMenu(array &$menu) {
    $groupId = $this->getCaseTypeCategoryGroupId();
    if (empty($groupId)) {
      return;
    }

    // Find the Civicase menu.
    $caseID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'CiviCase', 'id', 'name');
    $administerID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Administer', 'id', 'name');
    $civicaseSettings = &$menu[$administerID]['child'][$caseID];

    $desiredWeight = $this->moveMenuDown($civicaseSettings['child'], 'Case Types');

    $menu[$administerID]['child'][$caseID]['child'][] = [
      'attributes' => [
        'label' => ts('CiviCase Instances'),
        'name' => 'CiviCase Instances',
        'url' => "civicrm/admin/options?gid=$groupId&reset=1",
        'permission' => 'access all cases and activities',
        'operator' => 'OR',
        'separator' => 1,
        'parentID' => $caseID,
        'active' => 1,
        'weight' => $desiredWeight,
      ],
    ];
  }

  /**
   * Returnd the ID of the case type category option group.
   */
  private function getCaseTypeCategoryGroupId() {
    $optionGroups = OptionGroup::get(FALSE)
      ->addSelect('id')
      ->addWhere('name', '=', 'case_type_categories')
      ->setLimit(25)
      ->execute();

    return $optionGroups[0]["id"] ?? NULL;
  }

}
