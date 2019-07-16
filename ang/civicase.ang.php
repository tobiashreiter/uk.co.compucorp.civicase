<?php

/**
 * @file
 * Declares an Angular module which can be autoloaded in CiviCRM.
 *
 * See also:
 * http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules.
 */

use Civi\CCase\Utils as Utils;

Civi::resources()
  ->addPermissions([
    'administer CiviCase', 'administer CiviCRM',
    'access all cases and activities', 'add cases', 'basic case information',
  ])
  ->addScriptFile('org.civicrm.shoreditch', 'base/js/affix.js', 1000, 'html-header')
  ->addSetting([
    'config' => [
      'enableComponents' => CRM_Core_Config::singleton()->enableComponents,
      'user_contact_id' => (int) CRM_Core_Session::getLoggedInContactID(),
    ],
  ]);
// The following changes are only relevant to the full-page app.
if (CRM_Utils_System::getUrlPath() == 'civicrm/case/a') {
  // Add shoreditch custom css if not already present.
  if (!civicrm_api3('Setting', 'getvalue', ['name' => "customCSSURL"])) {
    Civi::resources()
      ->addStyleFile('org.civicrm.shoreditch', 'css/custom-civicrm.css', 99, 'html-header');
  }
  CRM_Utils_System::resetBreadCrumb();
  $breadcrumb = [
    [
      'title' => ts('Home'),
      'url' => CRM_Utils_System::url(),
    ],
    [
      'title' => ts('CiviCRM'),
      'url' => CRM_Utils_System::url('civicrm', 'reset=1'),
    ],
    [
      'title' => ts('Case Dashboard'),
      'url' => CRM_Utils_System::url('civicrm/case/a/#/case'),
    ],
  ];
  CRM_Utils_System::appendBreadCrumb($breadcrumb);
}
$options = [
  'activityTypes' => 'activity_type',
  'activityStatuses' => 'activity_status',
  'caseStatuses' => 'case_status',
  'priority' => 'priority',
  'activityCategories' => 'activity_category',
];
foreach ($options as &$option) {
  $result = civicrm_api3('OptionValue', 'get', [
    'return' => ['value', 'label', 'color', 'icon', 'name', 'grouping'],
    'option_group_id' => $option,
    'is_active' => 1,
    'options' => ['limit' => 0, 'sort' => 'weight'],
  ]);
  $option = [];
  foreach ($result['values'] as $item) {
    $key = $item['value'];
    CRM_Utils_Array::remove($item, 'id', 'value');
    $option[$key] = $item;
  }
}
$caseTypes = civicrm_api3('CaseType', 'get', [
  'return' => ['name', 'title', 'description', 'definition'],
  'options' => ['limit' => 0, 'sort' => 'weight'],
  'is_active' => 1,
]);
foreach ($caseTypes['values'] as &$item) {
  CRM_Utils_Array::remove($item, 'id', 'is_forkable', 'is_forked');
}
$options['caseTypes'] = $caseTypes['values'];
$result = civicrm_api3('RelationshipType', 'get', [
  'is_active' => 1,
  'options' => ['limit' => 0],
]);
$options['relationshipTypes'] = $result['values'];
$options['fileCategories'] = CRM_Civicase_FileCategory::getCategories();
$options['activityStatusTypes'] = [
  'incomplete' => array_keys(\CRM_Activity_BAO_Activity::getStatusesByType(CRM_Activity_BAO_Activity::INCOMPLETE)),
  'completed' => array_keys(\CRM_Activity_BAO_Activity::getStatusesByType(CRM_Activity_BAO_Activity::COMPLETED)),
  'cancelled' => array_keys(\CRM_Activity_BAO_Activity::getStatusesByType(CRM_Activity_BAO_Activity::CANCELLED)),
];
$result = civicrm_api3('CustomGroup', 'get', [
  'sequential' => 1,
  'return' => ['extends_entity_column_value', 'title', 'extends'],
  'extends' => ['IN' => ['Case', 'Activity']],
  'is_active' => 1,
  'options' => ['sort' => 'weight'],
  'api.CustomField.get' => [
    'is_active' => 1,
    'is_searchable' => 1,
    'return' => [
      'label', 'html_type', 'data_type', 'is_search_range',
      'filter', 'option_group_id',
    ],
    'options' => ['sort' => 'weight'],
  ],
]);
$options['customSearchFields'] = $options['customActivityFields'] = [];
foreach ($result['values'] as $group) {
  if (!empty($group['api.CustomField.get']['values'])) {
    if ($group['extends'] == 'Case') {
      if (!empty($group['extends_entity_column_value'])) {
        $group['caseTypes'] = CRM_Utils_Array::collect('name', array_values(array_intersect_key($caseTypes['values'], array_flip($group['extends_entity_column_value']))));
      }
      foreach ($group['api.CustomField.get']['values'] as $field) {
        $group['fields'][] = Utils::formatCustomSearchField($field);
      }
      unset($group['api.CustomField.get']);
      $options['customSearchFields'][] = $group;
    }
    else {
      foreach ($group['api.CustomField.get']['values'] as $field) {
        $options['customActivityFields'][] = Utils::formatCustomSearchField($field) + ['group' => $group['title']];
      }
    }
  }
}
// Case tags.
$options['tags'] = CRM_Core_BAO_Tag::getColorTags('civicrm_case');
$options['tagsets'] = CRM_Utils_Array::value('values', civicrm_api3('Tag', 'get', [
  'sequential' => 1,
  'return' => ["id", "name"],
  'used_for' => ['LIKE' => "%civicrm_case%"],
  'is_tagset' => 1,
]));
// Bulk actions for case list.
// we put this here so it can be modified by other extensions.
$options['caseActions'] = [
  [
    'title' => ts('Change Case Status'),
    'action' => 'changeStatus(cases)',
    'icon' => 'fa-pencil-square-o',
  ],
  [
    'title' => ts('Edit Tags'),
    'action' => 'editTags(cases[0])',
    'icon' => 'fa-tags',
    'number' => 1,
  ],
  [
    'title' => ts('Print Case'),
    'action' => 'print(cases[0])',
    'number' => 1,
    'icon' => 'fa-print',
  ],
  [
    'title' => ts('Email Case Manager'),
    'action' => 'emailManagers(cases)',
    'icon' => 'fa-envelope-o',
  ],
  [
    'title' => ts('Print/Merge Document'),
    'action' => 'printMerge(cases)',
    'icon' => 'fa-file-pdf-o',
  ],
  [
    'title' => ts('Export Cases'),
    'action' => 'exportCases(cases)',
    'icon' => 'fa-file-excel-o',
  ],
  [
    'title' => ts('Link Cases'),
    'action' => 'linkCases(cases[0])',
    'number' => 1,
    'icon' => 'fa-link',
  ],
  [
    'title' => ts('Link 2 Cases'),
    'action' => 'linkCases(cases[0], cases[1])',
    'number' => 2,
    'icon' => 'fa-link',
  ],
];
if (CRM_Core_Permission::check('administer CiviCase')) {
  $options['caseActions'][] = [
    'title' => ts('Merge 2 Cases'),
    'number' => 2,
    'action' => 'mergeCases(cases)',
    'icon' => 'fa-compress',
  ];
  $options['caseActions'][] = [
    'title' => ts('Lock Case'),
    'action' => 'lockCases(cases[0])',
    'number' => 1,
    'icon' => 'fa-lock',
  ];
}
if (CRM_Core_Permission::check('delete in CiviCase')) {
  $options['caseActions'][] = [
    'title' => ts('Delete Case'),
    'action' => 'deleteCases(cases)',
    'icon' => 'fa-trash',
  ];
}
// Add webforms with cases attached to menu.
$items = [];

$webformsToDisplay = Civi::settings()->get('civi_drupal_webforms');
if (isset($webformsToDisplay)) {
  $allowedWebforms = [];
  foreach ($webformsToDisplay as $webformNode) {
    $allowedWebforms[] = $webformNode['nid'];
  }
  $webforms = civicrm_api3('Case', 'getwebforms');
  if (isset($webforms['values'])) {
    foreach ($webforms['values'] as $webform) {
      if (!in_array($webform['nid'], $allowedWebforms)) {
        continue;
      }

      $client = get_client_delta_from_webform($webform['nid']);

      $items[] = [
        'title' => $webform['title'],
        'action' => 'gotoWebform(cases[0], "' . $webform['path'] . '", ' . $client . ')',
        'icon' => 'fa-link',
      ];
    }
    $options['caseActions'][] = [
      'title' => ts('Webforms'),
      'action' => '',
      'icon' => 'fa-file-text-o',
      'items' => $items,
    ];
  }
}

// Contact tasks.
$contactTasks = CRM_Contact_Task::permissionedTaskTitles(CRM_Core_Permission::getPermission());
$options['contactTasks'] = [];
foreach (CRM_Contact_Task::$_tasks as $id => $value) {
  if (isset($contactTasks[$id]) && isset($value['url'])) {
    $options['contactTasks'][$id] = $value;
  }
}
// Exposed settings:
$options['allowMultipleCaseClients'] = (bool) Civi::settings()->get('civicaseAllowMultipleClients');
$options['allowCaseLocks'] = (bool) Civi::settings()->get('civicaseAllowCaseLocks');

// Retrieve civicase webform URL.
$allowCaseWebform = Civi::settings()->get('civicaseAllowCaseWebform');
$options['newCaseWebformUrl'] = $allowCaseWebform ? Civi::settings()->get('civicaseWebformUrl') : NULL;
$options['newCaseWebformClient'] = 'cid';
if ($options['newCaseWebformUrl']) {
  $path = explode('/', $options['newCaseWebformUrl']);
  $nid = array_pop($path);
  $client = get_client_delta_from_webform($nid);
  if ($client) {
    $options['newCaseWebformClient'] = 'cid' . $client;
  }
}

if (!function_exists('glob_recursive')) {

  /**
   * Recursive Glob function.
   *
   * Source: http://php.net/manual/en/function.glob.php#106595
   * Does not support flag GLOB_BRACE.
   */
  function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);

    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
      $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }

    return $files;
  }

}

/**
 * Returns the contact number of the client for given webform id.
 *
 * @param int $webform_id
 *   Webform id.
 *
 * @return int
 *   Contact Number.
 */
function get_client_delta_from_webform($webform_id) {
  $node = node_load($webform_id);
  $data = $node->webform_civicrm['data'];
  $client = 0;
  if (isset($data['case'][1]['case'][1]['client_id'])) {
    $clients = $data['case'][1]['case'][1]['client_id'];
    $client = reset($clients);
  }

  return $client;
}

/**
 * Get a list of JS files.
 */
function get_js_files() {
  return array_merge([
    // At the moment, it's safe to include this multiple times.
    // deduped by resource manager.
    'assetBuilder://visual-bundle.js',
    'ang/civicase.js',
  ], glob_recursive(dirname(__FILE__) . '/civicase/*.js'));
}

return [
  'js' => get_js_files(),
  'css' => [
    // At the moment, it's safe to include this multiple times.
    // deduped by resource manager.
    'assetBuilder://visual-bundle.css',
    'css/*.css',
    'ang/civicase/*.css',
  ],
  'partials' => [
    'ang/civicase',
  ],
  'settings' => $options,
  'requires' => [
    'crmAttachment', 'crmUi', 'crmUtil', 'ngRoute', 'angularFileUpload',
    'bw.paging', 'crmRouteBinder', 'crmResource', 'ui.bootstrap',
    'uibTabsetClass', 'dialogService',
  ],
  'basePages' => [],
];
