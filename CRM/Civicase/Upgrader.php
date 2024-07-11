<?php

use CRM_Civicase_ExtensionUtil as E;
use CRM_Civicase_Helper_CaseUrl as CaseUrlHelper;
use CRM_Civicase_Service_CaseCategoryInstance as CaseCategoryInstance;
use CRM_Civicase_Setup_AddCaseCategoryWordReplacementOptionGroup as AddCaseCategoryWordReplacementOptionGroup;
use CRM_Civicase_Setup_AddChangeCaseRoleDateActivityTypes as AddChangeCaseRoleDateActivityTypes;
use CRM_Civicase_Setup_AddManageWorkflowMenu as AddManageWorkflowMenu;
use CRM_Civicase_Setup_AddMyActivitiesMenu as AddMyActivitiesMenu;
use CRM_Civicase_Setup_AddSingularLabels as AddSingularLabels;
use CRM_Civicase_Setup_CaseCategoryInstanceSupport as CaseCategoryInstanceSupport;
use CRM_Civicase_Setup_CaseTypeCategorySupport as CaseTypeCategorySupport;
use CRM_Civicase_Setup_CreateCasesOptionValue as CreateCasesOptionValue;
use CRM_Civicase_Setup_CreateSafeFileExtensionOptionValue as CreateSafeFileExtensionOptionValue;
use CRM_Civicase_Setup_Manage_CaseSalesOrderStatusManager as CaseSalesOrderStatusManager;
use CRM_Civicase_Setup_Manage_CaseTypeCategoryFeaturesManager as CaseTypeCategoryFeaturesManager;
use CRM_Civicase_Setup_Manage_MembershipTypeCustomFieldManager as MembershipTypeCustomFieldManager;
use CRM_Civicase_Setup_Manage_QuotationTemplateManager as QuotationTemplateManager;
use CRM_Civicase_Setup_MoveCaseTypesToCasesCategory as MoveCaseTypesToCasesCategory;
use CRM_Civicase_Setup_ProcessCaseCategoryForCustomGroupSupport as ProcessCaseCategoryForCustomGroupSupport;
use CRM_Civicase_Uninstall_RemoveCustomGroupSupportForCaseCategory as RemoveCustomGroupSupportForCaseCategory;

/**
 * Collection of upgrade steps.
 */
class CRM_Civicase_Upgrader extends CRM_Extension_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Tasks to perform when the module is installed.
   */
  public function install() {
    CRM_Core_BAO_ConfigSetting::enableComponent('CiviCase');

    // Set activity categories.
    $categories = [
      'milestone' => [
        'Open Case',
      ],
      'communication' => [
        'Meeting',
        'Phone Call',
        'Email',
        'SMS',
        'Inbound Email',
        'Follow up',
        'Print PDF Letter',
      ],
      'system' => [
        'Change Case Type',
        'Change Case Status',
        'Change Case Subject',
        'Change Custom Data',
        'Change Case Start Date',
        'Assign Case Role',
        'Remove Case Role',
        'Merge Case',
        'Reassigned Case',
        'Link Cases',
        'Change Case Tags',
        'Add Client To Case',
      ],
    ];
    foreach ($categories as $grouping => $activityTypes) {
      civicrm_api3('OptionValue', 'get', [
        'return' => 'id',
        'option_group_id' => 'activity_type',
        'name' => ['IN' => $activityTypes],
        'api.OptionValue.create' => [
          'option_group_id' => 'activity_type',
          'grouping' => $grouping,
        ],
      ]);
    }

    $this->addAllOptionValues();

    $steps = [
      new CaseTypeCategorySupport(),
      new CaseCategoryInstanceSupport(),
      new AddCaseCategoryWordReplacementOptionGroup(),
      new CreateCasesOptionValue(),
      new MoveCaseTypesToCasesCategory(),
      new CreateSafeFileExtensionOptionValue(),
      new ProcessCaseCategoryForCustomGroupSupport(),
      new AddChangeCaseRoleDateActivityTypes(),
      new AddSingularLabels(),
      new AddMyActivitiesMenu(),
    ];
    foreach ($steps as $step) {
      $step->apply();
    }

    // Set grouping for existing statuses.
    $allowedStatuses = [
      'Scheduled' => 'none,task,file,communication,milestone,system',
      'Completed' => 'none,task,file,communication,milestone,alert,system',
      'Cancelled' => 'none,communication,milestone,alert',
      'Left Message' => 'none,communication,milestone',
      'Unreachable' => 'none,communication,milestone',
      'Not Required' => 'none,task,milestone',
      'Available' => 'none,milestone',
      'No_show' => 'none,milestone',
    ];
    foreach ($allowedStatuses as $status => $grouping) {
      civicrm_api3('OptionValue', 'get', [
        'option_group_id' => 'activity_status',
        'name' => $status,
        'return' => 'id',
        'api.OptionValue.create' => [
          'option_group_id' => 'activity_status',
          'grouping' => $grouping,
        ],
      ]);
    }

    // Set status colors.
    $colors = [
      'activity_status' => [
        'Scheduled' => '#42afcb',
        'Completed' => '#8ec68a',
        'Left Message' => '#eca67f',
        'Available' => '#5bc0de',
      ],
      'case_status' => [
        'Open' => '#42afcb',
        'Closed' => '#4d5663',
        'Urgent' => '#e6807f',
      ],
    ];
    foreach ($colors as $optionGroup => $statuses) {
      foreach ($statuses as $status => $color) {
        civicrm_api3('OptionValue', 'get', [
          'option_group_id' => $optionGroup,
          'name' => $status,
          'api.OptionValue.setvalue' => [
            'field' => 'color',
            'value' => $color,
          ],
        ]);
      }
    }

    if (Civi::settings()->get('civicaseAllowMultipleClients') === 'default') {
      Civi::settings()->set('civicaseAllowMultipleClients', '1');
    }

    if (!Civi::settings()->hasExplict('recordGeneratedLetters')) {
      Civi::settings()->set('recordGeneratedLetters', 'combined-attached');
    }

    $this->createManageCasesMenuItem();
    (new CaseTypeCategoryFeaturesManager())->create();
    (new CaseSalesOrderStatusManager())->create();
    (new QuotationTemplateManager())->create();
    (new MembershipTypeCustomFieldManager())->create();
  }

  /**
   * Creates 'Manage Cases' menu item for Cases navigation.
   */
  private function createManageCasesMenuItem() {
    $this->addNav([
      'label' => E::ts('Manage Cases'),
      'name' => 'Manage Cases',
      'url' => CaseUrlHelper::getUrlByRouteType('all'),
      'permission' => 'access my cases and activities,access all cases and activities',
      'operator' => 'OR',
      'separator' => 0,
      'parent_name' => 'Cases',
    ]);

    CRM_Core_BAO_Navigation::resetNavigation();

    return TRUE;
  }

  /**
   * Tasks to perform after the module is installed.
   */
  public function postInstall() {
  }

  /**
   * Remove extension data when uninstalled.
   */
  public function uninstall() {
    try {
      civicrm_api3('OptionValue', 'get', [
        'return' => ['id'],
        'option_group_id' => 'activity_category',
        'options' => ['limit' => 0],
        'api.OptionValue.delete' => [],
      ]);
    }
    catch (Exception $e) {
    }
    try {
      civicrm_api3('OptionGroup', 'get', [
        'return' => ['id'],
        'name' => 'activity_category',
        'api.OptionGroup.delete' => [],
      ]);
    }
    catch (Exception $e) {
    }
    // Delete unused activity types.
    foreach (['File', 'Alert'] as $type) {
      try {
        $acts = civicrm_api3('Activity', 'getcount', [
          'activity_type_id' => $type,
        ]);
        if (empty($acts['result'])) {
          civicrm_api3('OptionValue', 'get', [
            'return' => ['id'],
            'option_group_id' => 'activity_type',
            'name' => $type,
            'api.OptionValue.delete' => [],
          ]);
        }
      }
      catch (Exception $e) {
      }
    }
    // Delete unused activity statuses.
    foreach (['Unread', 'Draft'] as $status) {
      try {
        $acts = civicrm_api3('Activity', 'getcount', [
          'status_id' => $status,
        ]);
        if (empty($acts['result'])) {
          civicrm_api3('OptionValue', 'get', [
            'return' => ['id'],
            'option_group_id' => 'activity_status',
            'name' => $status,
            'api.OptionValue.delete' => [],
          ]);
        }
      }
      catch (Exception $e) {
      }
    }

    $this->removeNav('Manage Cases');

    $steps = [
      new RemoveCustomGroupSupportForCaseCategory(),
    ];
    foreach ($steps as $step) {
      $step->apply();
    }

    (new CaseTypeCategoryFeaturesManager())->remove();
    (new CaseSalesOrderStatusManager())->remove();
    (new QuotationTemplateManager())->remove();
  }

  /**
   * Fixes original id of followup activities.
   *
   * Fixes original id of followup activities to point to the original activity
   * and not a revision.
   *
   * @todo This is a WIP (untested) and not yet called from anywhere.
   * When it's ready we can change its name to upgrade_000X and call it from
   * the installer.
   */
  public function fixActivityRevisions() {
    $sql = 'UPDATE civicrm_activity a, civicrm_activity b
      SET a.parent_id = b.original_id
      WHERE a.parent_id = b.id
      AND b.original_id IS NOT NULL';
    CRM_Core_DAO::executeQuery($sql);
    // @todo before we uncomment the below, need to migrate this history
    // to advanced logging table.
    // CRM_Core_DAO::executeQuery('DELETE FROM civicrm_activity WHERE
    // original_id IS NOT NULL');.
  }

  /**
   * Adds an option value if it doesn't already exist.
   *
   * Weight and value are calculated as needed.
   *
   * @param array $params
   *   Parameters.
   */
  protected function addOptionValue(array $params) {
    $optionGroup = $params['option_group_id'];
    $existing = civicrm_api3('OptionValue', 'get', [
      'option_group_id' => $optionGroup,
      'name' => $params['name'],
      'return' => 'id',
      'options' => ['limit' => 1],
    ]);
    if (!empty($existing['id'])) {
      $params['id'] = $existing['id'];
    }
    else {
      if (empty($params['value'])) {
        $sql = "SELECT MAX(ROUND(value)) + 1 FROM civicrm_option_value WHERE option_group_id = (SELECT id FROM civicrm_option_group WHERE name = '$optionGroup')";
        $params['value'] = CRM_Core_DAO::singleValueQuery($sql);
      }
      $sql = "SELECT MAX(ROUND(weight)) + 1 FROM civicrm_option_value WHERE option_group_id = (SELECT id FROM civicrm_option_group WHERE name = '$optionGroup')";
      $params['weight'] = CRM_Core_DAO::singleValueQuery($sql);
    }
    civicrm_api3('OptionValue', 'create', $params);
  }

  /**
   * Adds all the necessary Option Values.
   */
  private function addAllOptionValues() {
    // Create activity types.
    $this->addOptionValue([
      'option_group_id' => 'activity_type',
      'label' => E::ts('Alert'),
      'name' => 'Alert',
      'grouping' => 'alert',
      'is_reserved' => 0,
      'description' => E::ts('Alerts to display in cases'),
      'component_id' => 'CiviCase',
      'icon' => 'fa-exclamation',
    ]);
    $this->addOptionValue([
      'option_group_id' => 'activity_type',
      'label' => E::ts('File Upload'),
      'name' => 'File Upload',
      'grouping' => 'file',
      'is_reserved' => 0,
      'description' => E::ts('Add files to a case'),
      'component_id' => 'CiviCase',
      'icon' => 'fa-file',
    ]);
    $this->addOptionValue([
      'option_group_id' => 'activity_type',
      'label' => E::ts('Remove Client From Case'),
      'name' => 'Remove Client From Case',
      'grouping' => 'system',
      'is_reserved' => 0,
      'description' => E::ts('Client removed from multi-client case'),
      'component_id' => 'CiviCase',
      'icon' => 'fa-user-times',
    ]);

    // Create activity statuses.
    $this->addOptionValue([
      'option_group_id' => 'activity_status',
      'label' => E::ts('Unread'),
      'name' => 'Unread',
      'grouping' => 'communication',
      'is_reserved' => 0,
      'color' => '#d9534f',
    ]);
    $this->addOptionValue([
      'option_group_id' => 'activity_status',
      'label' => E::ts('Draft'),
      'name' => 'Draft',
      'grouping' => 'communication',
      'is_reserved' => 0,
      'color' => '#c2cfd8',
    ]);
  }

  /**
   * Add Nav.
   *
   * @param array $menuItem
   *   Menu Item.
   */
  public function addNav(array $menuItem) {
    $this->removeNav($menuItem['name']);

    $menuItem['is_active'] = 1;
    $menuItem['parent_id'] = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', $menuItem['parent_name'], 'id', 'name');
    unset($menuItem['parent_name']);
    CRM_Core_BAO_Navigation::add($menuItem);
  }

  /**
   * Toggle Nav.
   *
   * @param string $name
   *   The name of the item in `civicrm_navigation`.
   * @param bool $isActive
   *   Whether to enable/disable the nav.
   */
  protected function toggleNav($name, $isActive) {
    CRM_Core_DAO::executeQuery("UPDATE `civicrm_navigation` SET is_active = %2 WHERE name IN (%1)", [
      1 => [$name, 'String'],
      2 => [$isActive ? 1 : 0, 'Int'],
    ]);
  }

  /**
   * Remove nav.
   *
   * @param string $name
   *   The name of the item in `civicrm_navigation`.
   */
  protected function removeNav($name) {
    CRM_Core_DAO::executeQuery("DELETE FROM `civicrm_navigation` WHERE name IN (%1)", [
      1 => [$name, 'String'],
    ]);
  }

  /**
   * Re-enable the extension's parts.
   */
  public function enable() {
    $this->swapCaseMenuItems();

    $this->toggleNav('Manage Cases', TRUE);

    $instanceObj = new CaseCategoryInstance();
    $instanceObj->assignInstanceForExistingCaseCategories();

    $workflowMenu = new AddManageWorkflowMenu();
    $workflowMenu->apply();

    (new CaseTypeCategoryFeaturesManager())->enable();
    (new CaseSalesOrderStatusManager())->enable();
    (new QuotationTemplateManager())->enable();
  }

  /**
   * Disable the extension's parts without removing them.
   */
  public function disable() {
    $this->swapCaseMenuItems();

    $this->toggleNav('Manage Cases', FALSE);
    (new CaseTypeCategoryFeaturesManager())->disable();
    (new CaseSalesOrderStatusManager())->disable();
    (new QuotationTemplateManager())->disable();
  }

  /**
   * Swaps Case Menu Items.
   *
   * Swaps weight and has_separator values between 'Find Cases'
   * and 'Manage Cases' menu items.
   */
  private function swapCaseMenuItems() {
    $findCasesMenuItem = $this->getCaseMenuItem('Find Cases');
    $manageCasesMenuItem = $this->getCaseMenuItem('Manage Cases');

    if (!$findCasesMenuItem || !$manageCasesMenuItem) {
      return TRUE;
    }

    // Updating 'Find Cases' menu item.
    civicrm_api3('Navigation', 'create', [
      'id' => $findCasesMenuItem['id'],
      'weight' => !empty($manageCasesMenuItem['weight']) ? $manageCasesMenuItem['weight'] : NULL,
      'has_separator' => $manageCasesMenuItem['has_separator'],
    ]);

    // Updating 'Manage Cases' menu item.
    civicrm_api3('Navigation', 'create', [
      'id' => $manageCasesMenuItem['id'],
      'weight' => !empty($findCasesMenuItem['weight']) ? $findCasesMenuItem['weight'] : NULL,
      'has_separator' => $findCasesMenuItem['has_separator'],
    ]);

    CRM_Core_BAO_Navigation::resetNavigation();
  }

  /**
   * Returns an array containing Case menu item for specified name.
   *
   * Returns NULL if menu item is not found.
   *
   * @param string $name
   *   Menu Item Name.
   *
   * @return array|null
   *   The Menu Item.
   */
  private function getCaseMenuItem($name) {
    $result = civicrm_api3('Navigation', 'get', [
      'sequential' => 1,
      'parent_id' => 'Cases',
      'name' => $name,
      'options' => ['limit' => 1],
    ]);

    if (empty($result['id'])) {
      return NULL;
    }

    return $result['values'][0];
  }

  /**
   * Checks if there are pending revisions.
   *
   * @inheritdoc
   */
  public function hasPendingRevisions() {
    $revisions = $this->getRevisions();
    $currentRevisionNum = $this->getCurrentRevision();
    if (empty($revisions)) {
      return FALSE;
    }
    if (empty($currentRevisionNum)) {
      return TRUE;
    }
    return ($currentRevisionNum < max($revisions));
  }

  /**
   * Enqueue Pending Revisions.
   *
   * @inheritdoc
   */
  public function enqueuePendingRevisions() {
    $currentRevisionNum = (int) $this->getCurrentRevision();
    foreach ($this->getRevisions() as $revisionClass => $revisionNum) {
      if ($revisionNum <= $currentRevisionNum) {
        continue;
      }
      $title = ts('Upgrade %1 to revision %2', [
        1 => $this->extensionName,
        2 => $revisionNum,
      ]);
      $upgradeTask = new CRM_Queue_Task(
        [get_class($this), 'runStepUpgrade'],
        [(new $revisionClass())],
        $title
      );
      $this->queue->createItem($upgradeTask);
      $this->appendTask($title, 'setCurrentRevision', $revisionNum);
    }
  }

  /**
   * This is a callback for running step upgraders from the queue.
   *
   * #ToDO Removed Object Type hinting. Not compatible with PHP < 7.2.
   *
   * @param CRM_Queue_TaskContext $context
   *   Context.
   * @param object $step
   *   Step.
   *
   * @return true
   *   The queue requires that true is returned on successful upgrade, but we
   *   use exceptions to indicate an error instead.
   */
  public static function runStepUpgrade(CRM_Queue_TaskContext $context, $step) {
    $step->apply();
    return TRUE;
  }

  /**
   * Get a list of revisions.
   *
   * @return array
   *   An array of revisions sorted by the upgrader class as keys
   */
  public function getRevisions() {
    $extensionRoot = __DIR__;
    $stepClassFiles = glob($extensionRoot . '/Upgrader/Steps/Step*.php');
    $sortedKeyedClasses = [];
    foreach ($stepClassFiles as $file) {
      $class = $this->getUpgraderClassnameFromFile($file);
      $numberPrefix = 'Steps_Step';
      $startPos = strpos($class, $numberPrefix) + strlen($numberPrefix);
      $revisionNum = (int) substr($class, $startPos);
      $sortedKeyedClasses[$class] = $revisionNum;
    }
    asort($sortedKeyedClasses, SORT_NUMERIC);

    return $sortedKeyedClasses;
  }

  /**
   * Gets the PEAR style classname from an upgrader file.
   *
   * @param string $file
   *   Filename.
   *
   * @return string
   *   Upgrader class name.
   */
  private function getUpgraderClassnameFromFile($file) {
    $file = str_replace(realpath(__DIR__ . '/../../'), '', $file);
    $file = str_replace('.php', '', $file);
    $file = str_replace('/', '_', $file);
    return ltrim($file, '_');
  }

}
