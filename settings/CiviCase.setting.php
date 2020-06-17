<?php

/**
 * @file
 * CiviCase Setting file.
 */

$setting = [
  'civicaseAllowCaseLocks' => [
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'civicaseAllowCaseLocks',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => FALSE,
    'html_type' => 'radio',
    'add' => '4.7',
    'title' => 'Allow cases to be locked',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'This will allow cases to be locked for certain contacts.',
    'help_text' => '',
  ],
  'civicaseShowComingSoonCaseSummaryBlock' => [
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'civicaseShowComingSoonCaseSummaryBlock',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => TRUE,
    'html_type' => 'radio',
    'add' => '4.7',
    'title' => 'Show "Coming Soon" section on Case Summary',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'This configuration controls the visibility of the coming soon section on the case summary screen which has Next Milestone, Next Activity and the Case Calendar.',
  ],
  'civicaseAllowLinkedCasesTab' => [
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'civicaseAllowLinkedCasesTab',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => FALSE,
    'html_type' => 'radio',
    'add' => '4.7',
    'title' => 'Allow linked cases tab',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'This will allow linked cases to be viewed on a separate tab.',
    'help_text' => '',
  ],
  'civicaseShowWebformsListSeparately' => [
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'civicaseShowWebformsListSeparately',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => FALSE,
    'html_attributes' => [
      'class' => 'civicase__settings__show-webform',
    ],
    'html_type' => 'radio',
    'add' => '4.7',
    'title' => 'Show Webforms list in a separate dropdown',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'This will show the webforms list in a separate dropdown.',
    'help_text' => '',
  ],
  'civicaseWebformsDropdownButtonLabel' => [
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'civicaseWebformsDropdownButtonLabel',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => [
      'class' => 'civicase__settings__webform-button-label',
      'size' => 64,
      'maxlength' => 64,
    ],
    'html_type' => 'text',
    'default' => 'Webforms',
    'add' => '4.7',
    'title' => 'Label for the Webforms dropdown button',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Label for the Webforms dropdown button',
    'help_text' => '',
  ],
];

$caseSetting = new CRM_Civicase_Service_CaseCategorySetting();
$caseCategoryWebFormSetting = $caseSetting->getForWebform();

return array_merge($setting, $caseCategoryWebFormSetting);
