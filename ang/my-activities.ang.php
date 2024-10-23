<?php

/**
 * @file
 * Declares an Angular module which can be autoloaded in CiviCRM.
 *
 * See also:
 * http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules.
 */

use CRM_Civicase_Helper_GlobRecursive as GlobRecursive;

/**
 * Get a list of JS files.
 *
 * @return array
 *   list of js files
 */
function get_my_activities_js_files() {
  return array_merge(
    [
      'ang/my-activities.js',
    ],
    GlobRecursive::getRelativeToExtension(
      'uk.co.compucorp.civicase',
      'ang/my-activities/*.js'
    )
  );
}

$requires = [
  'crmUi',
  'crmCaseType',
		'ngRoute',
  'dialogService',
		'civicase-base',
];

$requires = CRM_Workflow_Hook_addDependentAngularModules::invoke($requires);

return [
  'css' => [
    'css/*.css',
  ],
  'js' => get_my_activities_js_files(),
  'requires' => $requires,
  'partials' => [
    'ang/my-activities',
  ],
];
