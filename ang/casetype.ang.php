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
function get_casetype_js_files() {
  return array_merge([
    'ang/casetype.js',
  ], GlobRecursive::get(dirname(__FILE__) . '/casetype/*.js'));
}

return [
  'css' => [
    'css/*.css',
  ],
  'js' => get_casetype_js_files(),
  'settings' => $options,
  'requires' => [
    'crmUi',
    'ngRoute',
    'dialogService',
    'civicase-base',
  ],
  'partials' => [
    'ang/casetype',
  ],
];
