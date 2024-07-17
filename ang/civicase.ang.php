<?php

/**
 * @file
 * Declares an Angular module which can be autoloaded in CiviCRM.
 *
 * See also:
 * http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules.
 */

$requires = [
	'crmAttachment',
	'crmUi',
	'ngRoute',
	'angularFileUpload',
	'crmRouteBinder',
	'crmResource',
	'ui.bootstrap',
	'uibTabsetClass',
	'dialogService',
	'civicase-base',
];
$requires = CRM_Civicase_Hook_addDependentAngularModules::invoke($requires);

return [
  'js' => CRM_Civicase_Angular::get_js_files(),
  'css' => [
    // At the moment, it's safe to include this multiple times.
    // deduped by resource manager.
    // THR: this appears to throw an error, because it creates relUrl-assetBuilder resource format, which isn't supported?
    // 'assetBuilder://visual-bundle.css',
    'css/*.css',
  ],
  'partials' => [
    'ang/civicase',
  ],
  'permissions' => [
    'administer CiviCase',
    'administer CiviCRM',
    'access all cases and activities',
    'add cases',
    'basic case information',
    'access CiviCRM',
    'access my cases and activities',
  ],
  'settingsFactory' => ['CRM_Civicase_Angular', 'getOptions'],
  'requires' => $requires,
  'basePages' => [],
];
