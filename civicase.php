<?php

/**
 * @file
 * Extension file.
 */

use Civi\Angular\Manager;

require_once 'civicase.civix.php';

/**
 * Implements hook_civicrm_tabset().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tabset
 */
function civicase_civicrm_tabset($tabsetName, &$tabs, $context) {
  $useAng = FALSE;
  $hooks = [
    new CRM_Civicase_Hook_Tabset_CaseTabModifier(),
    new CRM_Civicase_Hook_Tabset_CaseCategoryTabAdd(),
    new CRM_Civicase_Hook_Tabset_ActivityTabModifier(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($tabsetName, $tabs, $context, $useAng);
  }

  if ($useAng) {
    $loader = Civi::service('angularjs.loader');
    $loader->addModules(['civicase', 'civicase-features']);
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civicase_civicrm_config(&$config) {
  _civicase_civix_civicrm_config($config);

  if (isset(Civi::$statics[__FUNCTION__])) {
    return;
  }
  Civi::$statics[__FUNCTION__] = 1;

  Civi::dispatcher()->addListener(
    'civi.api.prepare',
    ['CRM_Civicase_Event_Listener_ActivityFilter', 'onPrepare'],
    10
  );
  Civi::dispatcher()->addListener(
    'civi.api.respond',
    [
      'CRM_Civicase_Event_Listener_CaseTypeCategoryIsActiveToggler',
      'onRespond',
    ],
    10
  );
  Civi::dispatcher()->addListener(
    'civi.api.respond',
    ['CRM_Civicase_Event_Listener_CaseRoleCreation', 'onRespond'],
    10
  );
  Civi::dispatcher()->addListener(
    'civi.api.prepare',
    ['CRM_Civicase_Event_Listener_CaseRoleCreation', 'onPrepare'],
    10
  );

  Civi::dispatcher()->addListener(
    'civi.api.prepare',
    ['CRM_Civicase_Event_Listener_CaseCustomFields', 'loadOnDemand'],
    10
  );

  Civi::dispatcher()->addListener(
    'civi.token.list',
    ['CRM_Civicase_Hook_Tokens_SalesOrderTokens', 'listSalesOrderTokens']
  );

  Civi::dispatcher()->addListener(
    'civi.token.eval',
    ['CRM_Civicase_Hook_Tokens_SalesOrderTokens', 'evaluateSalesOrderTokens']
  );

  Civi::dispatcher()->addListener(
    'civi.token.eval',
    [
      'CRM_Civicase_Hook_Tokens_AddCaseCustomFieldsTokenValues',
      'evaluateCaseCustomFieldsTokens',
    ]
  );
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civicase_civicrm_install() {
  _civicase_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civicase_civicrm_enable() {
  _civicase_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_alterMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterMenu
 */
function civicase_civicrm_alterMenu(&$items) {
  $items['civicrm/case/activity']['ids_arguments']['json'][] = 'civicase_reload';
  $items['civicrm/activity']['ids_arguments']['json'][] = 'civicase_reload';
  $items['civicrm/activity/email/add']['ids_arguments']['json'][] = 'civicase_reload';
  $items['civicrm/case/email/add']['ids_arguments']['json'][] = 'civicase_reload';
  $items['civicrm/activity/pdf/add']['ids_arguments']['json'][] = 'civicase_reload';
  $items['civicrm/case/cd/edit']['ids_arguments']['json'][] = 'civicase_reload';
  $items['civicrm/export/standalone']['ids_arguments']['json'][] = 'civicase_reload';
}

/**
 * Implements hook_civicrm_buildForm().
 */
function civicase_civicrm_buildForm($formName, &$form) {
  $hooks = [
    new CRM_Civicase_Hook_BuildForm_CaseClientPopulator(),
    new CRM_Civicase_Hook_BuildForm_FilterCaseTypesByCategoryForNewCase(),
    new CRM_Civicase_Hook_BuildForm_FilterByCaseCategoryOnChangeCaseType(),
    new CRM_Civicase_Hook_BuildForm_CaseCategoryFormLabelTranslationForNewCase(),
    new CRM_Civicase_Hook_BuildForm_CaseCategoryFormLabelTranslationForChangeCase(),
    new CRM_Civicase_Hook_BuildForm_EnableCaseCategoryIconField(),
    new CRM_Civicase_Hook_BuildForm_CaseCategoryCustomGroupDisplay(),
    new CRM_Civicase_Hook_BuildForm_ModifyCaseTypesForAdvancedSearch(),
    new CRM_Civicase_Hook_BuildForm_AddCaseCategoryInstanceField(),
    new CRM_Civicase_Hook_BuildForm_AddStyleFieldToCaseCustomGroups(),
    new CRM_Civicase_Hook_BuildForm_RemoveExportActionFromReports(),
    new CRM_Civicase_Hook_BuildForm_RestrictCaseEmailContacts(),
    new CRM_Civicase_Hook_BuildForm_LimitRecipientFieldsToOnlySelectedContacts(),
    new CRM_Civicase_Hook_BuildForm_TokenTree(),
    new CRM_Civicase_Hook_BuildForm_LinkCaseActivityDefaultStatus(),
    new CRM_Civicase_Hook_BuildForm_HandleDraftActivities(),
    new CRM_Civicase_Hook_BuildForm_AddCaseCategoryCustomFields(),
    new CRM_Civicase_Hook_BuildForm_MakePdfFormSubjectRequired(),
    new CRM_Civicase_Hook_BuildForm_PdfFormButtonsLabelChange(),
    new CRM_Civicase_Hook_BuildForm_AddScriptToCreatePdfForm(),
    new CRM_Civicase_Hook_BuildForm_AddCaseCategoryFeaturesField(),
    new CRM_Civicase_Hook_BuildForm_AddQuotationsNotesToContributionSettings(),
    new CRM_Civicase_Hook_BuildForm_AddSalesOrderLineItemsToContribution(),
    new CRM_Civicase_Hook_BuildForm_AddEntityReferenceToCustomField(),
    new CRM_Civicase_Hook_BuildForm_AttachQuotationToInvoiceMail(),
    new CRM_Civicase_Hook_BuildForm_RefreshInvoiceListOnUpdate(),
    new CRM_Civicase_Hook_BuildForm_AddCaseActivityDateFormatToDateSettings(),
    new CRM_Civicase_Hook_BuildForm_FormatCaseActivityDateFormat(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($form, $formName);
  }

  // Display category option for activity types and activity statuses.
  if ($formName == 'CRM_Admin_Form_Options'
    && in_array($form->getVar('_gName'), [
      'activity_type',
      'activity_status',
    ])) {
    $options = civicrm_api3('optionValue', 'get', [
      'option_group_id' => 'activity_category',
      'is_active' => 1,
      'options' => ['limit' => 0, 'sort' => 'weight'],
    ]);
    $opts = [];
    if ($form->getVar('_gName') == 'activity_status') {
      $placeholder = ts('All');
      // Activity status can also apply to uncategorized activities.
      $opts[] = [
        'id' => 'none',
        'text' => ts('Uncategorized'),
      ];
    }
    else {
      $placeholder = ts('Uncategorized');
    }
    foreach ($options['values'] as $opt) {
      $opts[] = [
        'id' => $opt['name'],
        'text' => $opt['label'],
      ];
    }
    $form->add('select2', 'grouping', ts('Activity Category'), $opts, FALSE, [
      'class' => 'crm-select2',
      'multiple' => TRUE,
      'placeholder' => $placeholder,
    ]);
  }
  // Only show relevant statuses when editing an activity.
  if (is_a($form, 'CRM_Activity_Form_Activity') && $form->_action & (CRM_Core_Action::ADD + CRM_Core_Action::UPDATE)) {
    if (!empty($form->_activityTypeId) && $form->elementExists('status_id')) {
      $el = $form->getElement('status_id');
      $cat = civicrm_api3('OptionValue', 'getsingle', [
        'return' => 'grouping',
        'option_group_id' => "activity_type",
        'value' => $form->_activityTypeId,
      ]);
      $cat = !empty($cat['grouping']) ? explode(',', $cat['grouping']) : ['none'];
      $options = civicrm_api3('OptionValue', 'get', [
        'return' => ['label', 'value', 'grouping'],
        'option_group_id' => "activity_status",
        'options' => ['limit' => 0, 'sort' => 'weight'],
      ]);
      $newOptions = $el->_options = [];
      $newOptions[''] = ts('- select -');
      foreach ($options['values'] as $option) {
        if (empty($option['grouping']) || array_intersect($cat, explode(',', $option['grouping']))) {
          $newOptions[$option['value']] = $option['label'];
        }
      }
      $el->loadArray($newOptions);
    }
  }
  // If js requests a refresh of case data pass that request along.
  if (!empty($_REQUEST['civicase_reload'])) {
    $form->civicase_reload = json_decode($_REQUEST['civicase_reload'], TRUE);
  }

  $isSearchKit = CRM_Utils_Request::retrieve('sk', 'Positive');
  if ($formName == 'CRM_Contribute_Form_Task_PDF' && $isSearchKit) {
    $form->add('hidden', 'mail_task_from_sk', $isSearchKit);
  }

  if ($formName == 'CRM_Contribute_Form_Task_Invoice' && $isSearchKit) {
    $form->add('hidden', 'mail_task_from_sk', $isSearchKit);
    CRM_Core_Resources::singleton()->addScriptFile(
      CRM_Civicase_ExtensionUtil::LONG_NAME,
      'js/invoice-bulk-mail.js',
    );
    $form->setTitle(ts('Email Contribution Invoice'));
    $ids = CRM_Utils_Request::retrieve('id', 'Positive', $form, FALSE);
    $form->assign('totalSelectedContributions', count(explode(',', $ids)));
  }
}

/**
 * Implements hook_civicrm_validateForm().
 */
function civicase_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  $hooks = [
    new CRM_Civicase_Hook_ValidateForm_SaveActivityDraft(),
    new CRM_Civicase_Hook_ValidateForm_SaveCaseTypeCategory(),
    new CRM_Civicase_Hook_ValidateForm_SendBulkEmail(),
    new CRM_Civicase_Hook_ValidateForm_RemoveEmptyTargetContactFromActivity(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($formName, $fields, $files, $form, $errors);
  }
}

/**
 * Implements hook_civicrm_post().
 */
function civicase_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  $hooks = [
    new CRM_Civicase_Hook_Post_CaseSalesOrderPayment(),
    new CRM_Civicase_Hook_Post_CreateSalesOrderContribution(),
    new CRM_Civicase_Hook_Post_PopulateCaseCategoryForCaseType(),
    new CRM_Civicase_Hook_Post_CaseCategoryCustomGroupSaver(),
    new CRM_Civicase_Hook_Post_UpdateCaseTypeListForCaseCategoryCustomGroup(),
    new CRM_Civicase_Hook_Post_LinkCase(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($op, $objectName, $objectId, $objectRef);
  }
}

/**
 * Implements hook_civicrm_pre().
 */
function civicase_civicrm_pre($op, $objectName, $id, &$params) {
  $hooks = [
    new CRM_Civicase_Hook_Pre_DeleteSalesOrderContribution(),
    new CRM_Civicase_Hook_Pre_HandleCaseEmailActivity(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($op, $objectName, $id, $params);
  }
}

/**
 * Implements hook_civicrm_postProcess().
 */
function civicase_civicrm_postProcess($formName, &$form) {
  $hooks = [
    new CRM_Civicase_Hook_PostProcess_SetUserContextForSaveAndNewCase(),
    new CRM_Civicase_Hook_PostProcess_SaveCaseCategoryInstance(),
    new CRM_Civicase_Hook_PostProcess_CaseCategoryPostProcessor(),
    new CRM_Civicase_Hook_PostProcess_ActivityFormStatusWordReplacement(),
    new CRM_Civicase_Hook_PostProcess_RedirectToCaseDetails(),
    new CRM_Civicase_Hook_PostProcess_AttachEmailActivityToAllCases(),
    new CRM_Civicase_Hook_PostProcess_HandleDraftActivity(),
    new CRM_Civicase_Hook_PostProcess_SaveCaseCategoryCustomFields(),
    new CRM_Civicase_Hook_PostProcess_SaveCaseCategoryFeature(),
    new CRM_Civicase_Hook_PostProcess_SaveQuotationsNotesSettings(),
    new CRM_Civicase_Hook_PostProcess_SaveCaseActivityDateFormat(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($formName, $form);
  }

  if (!empty($form->civicase_reload)) {
    $api = civicrm_api3('Case', 'getdetails', ['check_permissions' => 1] + $form->civicase_reload);
    $form->ajaxResponse['civicase_reload'] = $api['values'];
  }

  if (
      in_array($formName, [
        'CRM_Contribute_Form_Task_Invoice', 'CRM_Contribute_Form_Task_PDF',
      ])
      && !empty($form->getVar('_submitValues')['mail_task_from_sk'])
    ) {
    CRM_Utils_System::redirect($_SERVER['HTTP_REFERER']);
  }
}

/**
 * Implements hook_civicrm_permission().
 */
function civicase_civicrm_permission(&$permissions) {
  $hooks = [
    new CRM_Civicase_Hook_Permissions_CaseCategory($permissions),
    new CRM_Civicase_Hook_Permissions_ExportCasesAndReports($permissions),
  ];

  foreach ($hooks as $hook) {
    $hook->run();
  }
}

/**
 * Implements hook_civicrm_apiWrappers().
 */
function civicase_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if ($apiRequest['entity'] == 'Case') {
    $wrappers[] = new CRM_Civicase_Api_Wrapper_CaseList();
    $wrappers[] = new CRM_Civicase_Api_Wrapper_CaseGetList();
  }
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 *
 * @link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_alterAPIPermissions/
 */
function civicase_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  $hooks = [
    new CRM_Civicase_Hook_alterAPIPermissions_CaseCategory(),
    new CRM_Civicase_Hook_alterAPIPermissions_CaseGetList(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($entity, $action, $params, $permissions);
  }
}

/**
 * Implements hook_civicrm_tokens().
 */
function civicase_civicrm_tokens(&$tokens) {
  $contactFieldsService = new CRM_Civicase_Service_ContactFieldsProvider();
  $contactCustomFieldsService = new CRM_Civicase_Service_ContactCustomFieldsProvider();
  $caseCustomFieldsService = new CRM_Civicase_Service_CaseCustomFieldsProvider();
  $hooks = [
    new CRM_Civicase_Hook_Tokens_AddContactTokens($contactFieldsService, $contactCustomFieldsService),
    new CRM_Civicase_Hook_Tokens_AddCaseTokenCategory($caseCustomFieldsService),
  ];
  foreach ($hooks as &$hook) {
    $hook->run($tokens);
  }
}

/**
 * Implements hook_civicrm_tokenValues().
 */
function civicase_civicrm_tokenValues(&$values, $cids, $job = NULL, $tokens = [], $context = NULL) {
  $contactFieldsService = new CRM_Civicase_Service_ContactFieldsProvider();
  $contactCustomFieldsService = new CRM_Civicase_Service_ContactCustomFieldsProvider();
  $caseTokenValuesHelper = new CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues();
  $hooks = [
    new CRM_Civicase_Hook_Tokens_AddContactTokensValues($contactFieldsService, $contactCustomFieldsService),
    new CRM_Civicase_Hook_Tokens_AddCaseCustomFieldsTokenValues($caseTokenValuesHelper),
  ];
  foreach ($hooks as &$hook) {
    $hook->run($values, $cids, $job, $tokens, $context);
  }
}

/**
 * Implements hook_civicrm_pageRun().
 */
function civicase_civicrm_pageRun(&$page) {
  $hooks = [
    new CRM_Civicase_Hook_PageRun_ViewCasePageRedirect(),
    new CRM_Civicase_Hook_PageRun_AddCaseAngularPageResources(),
    new CRM_Civicase_Hook_PageRun_AddContactPageSummaryResources(),
    new CRM_Civicase_Hook_PageRun_CaseCategoryCustomGroupListing(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($page);
  }
}

/**
 * Implements hook_civicrm_check().
 */
function civicase_civicrm_check(&$messages) {
  $check = new CRM_Civicase_Check();
  $newMessages = $check->checkAll();
  $messages = array_merge($messages, $newMessages);
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function civicase_civicrm_navigationMenu(&$menu) {
  $hooks = [
    new CRM_Civicase_Hook_NavigationMenu_AlterForCaseMenu(),
    new CRM_Civicase_Hook_NavigationMenu_CaseInstanceFeaturesMenu(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($menu);
  }
}

/**
 * Implements hook_civicrm_selectWhereClause().
 */
function civicase_civicrm_selectWhereClause($entity, &$clauses) {
  if ($entity === 'Case') {
    unset($clauses['id']);
  }

  $hooks = [
    new CRM_Civicase_Hook_SelectWhereClause_LimitCaseQueryToAccessibleCaseCategories(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($entity, $clauses);
  }
}

/**
 * Implements hook_civicrm_entityTypes().
 */
function civicase_civicrm_entityTypes(&$entityTypes) {
  _civicase_add_case_category_case_type_entity($entityTypes);
}

/**
 * Implements hook_civicrm_queryObjects().
 */
function civicase_civicrm_queryObjects(&$queryObjects, $type) {
  if ($type == 'Contact') {
    $queryObjects[] = new CRM_Civicase_BAO_Query_ContactLock();
  }
}

/**
 * Implements hook_civicrm_permission_check().
 */
function civicase_civicrm_permission_check($permission, &$granted, $contactId) {
  $hooks = [
    new CRM_Civicase_Hook_PermissionCheck_ActivityPageView(),
    new CRM_Civicase_Hook_PermissionCheck_CaseCategory(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($permission, $granted, $contactId);
  }
}

/**
 * Implements hook_civicrm_preProcess().
 */
function civicase_civicrm_preProcess($formName, &$form) {
  $hooks = [
    new CRM_Civicase_Hook_PreProcess_CaseCategoryWordReplacementsForNewCase(),
    new CRM_Civicase_Hook_PreProcess_CaseCategoryWordReplacementsForChangeCase(),
    new CRM_Civicase_Hook_PreProcess_AddCaseAdminSettings(),
    new CRM_Civicase_Hook_PreProcess_CaseTypeCategoryWebFormRedirect(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($formName, $form);
  }
}

/**
 * Implements hook_civicrm_summaryActions().
 */
function civicase_civicrm_summaryActions(&$actions, $contactID) {
  $hooks = [
    new CRM_Civicase_Hook_SummaryActions_AlterAddCaseAction(),
  ];

  foreach ($hooks as $hook) {
    $hook->run($actions, $contactID);
  }
}

/**
 * Implements hook_civicrm_alterAngular().
 */
function civicase_civicrm_alterAngular(Manager $angular) {
  if (CRM_Core_Permission::check(
    [['administer CiviCase', 'administer CiviCRM']]
  )) {
    $angular->add(CRM_Civicase_Hook_alterAngular_AngularChangeSet::getForCaseTypeCategoryField());
    $angular->add(CRM_Civicase_Hook_alterAngular_AngularChangeSet::getForHidingNewCaseTypeButton());
  }
}

/**
 * Adds Case Type Category field to the Case Type entity DAO.
 *
 * @param array $entityTypes
 *   Entity types array.
 */
function _civicase_add_case_category_case_type_entity(array &$entityTypes) {
  $caseTypeEntityName = isset($entityTypes['CRM_Case_DAO_CaseType']) ? 'CRM_Case_DAO_CaseType' : 'CaseType';

  $entityTypes[$caseTypeEntityName]['fields_callback'][] = function ($class, &$fields) {
    $fields['case_type_category'] = [
      'name' => 'case_type_category',
      'type' => CRM_Utils_Type::T_INT,
      'title' => ts('Case Type Category'),
      'description' => ts('FK to a civicrm_option_value (case_type_categories)'),
      'required' => FALSE,
      'where' => 'civicrm_case_type.case_type_category',
      'table_name' => 'civicrm_case_type',
      'entity' => 'CaseType',
      'bao' => 'CRM_Case_BAO_CaseType',
      'localizable' => 1,
      'html' => [
        'type' => 'Select',
      ],
      'pseudoconstant' => [
        'optionGroupName' => 'case_type_categories',
        'optionEditPath' => 'civicrm/admin/options/case_type_categories',
      ],
    ];
  };
}

/**
 * Implements hook_civicrm_alterMailParams().
 */
function civicase_civicrm_alterMailParams(&$params, $context) {
  $hooks = [
    new CRM_Civicase_Hook_alterMailParams_SubjectProcessor(),
    new CRM_Civicase_Hook_alterMailParams_AttachQuotation(),
  ];

  foreach ($hooks as &$hook) {
    $hook->run($params, $context);
  }
}
/**
 * Implements hook_civicrm_searchKitTasks().
 */
function civicase_civicrm_searchKitTasks(array &$tasks, bool $checkPermissions, ?int $userID) {
  if (empty($tasks['CaseSalesOrder'])) {
    return;
  }

  $actions = [];

  if (!empty($tasks['CaseSalesOrder']['delete'])) {
    $actions['delete'] = $tasks['CaseSalesOrder']['delete'];
    $actions['delete']['title'] = 'Delete Quotation(s)';
  }

  $actions['add_discount'] = [
    'module' => 'civicase-features',
    'icon' => 'fa-percent',
    'title' => ts('Add Discount'),
    'uiDialog' => ['templateUrl' => '~/civicase-features/quotations/directives/quotations-discount.directive.html'],
  ];

  $actions['create_contribution'] = [
    'module' => 'civicase-features',
    'icon'  => 'fa-credit-card',
    'title' => ts('Create Contribution(s)'),
    'uiDialog' => ['templateUrl' => '~/civicase-features/quotations/directives/quotations-contribution-bulk.directive.html'],
  ];

  $tasks['CaseSalesOrder'] = $actions;

}

/**
 * Implements hook_civicrm_searchTasks().
 */
function civicase_civicrm_searchTasks(string $objectName, array &$tasks) {
  if ($objectName === 'contribution') {
    $tasks['bulk_invoice'] = [
      'title' => ts('Send Invoice by email'),
      'class' => 'CRM_Contribute_Form_Task_Invoice',
      'icon' => 'fa-paper-plane-o',
      'url' => 'civicrm/contribute/task?reset=1&task_item=invoice&sk=1',
      'key' => 'invoice',
    ];

    foreach ($tasks as &$task) {
      if ($task['class'] === 'CRM_Contribute_Form_Task_PDF') {
        $task['url'] .= '&sk=1';
      }
    }
  }
}

/**
 * Implements hook_civicrm_alterContent().
 */
function civicase_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  if ($context == "form") {
    if ($tplName == "CRM/Activity/Form/Activity.tpl") {
      // See: templates/CRM/Core/Form/RecurringEntity.tpl#209
      // Ensure submit buttons will validate the form properly.
      $content = str_replace("#_qf_Activity_upload-top, #_qf_Activity_upload-bottom", "#_qf_Activity_upload-top, #_qf_Activity_upload-bottom, #_qf_Activity_submit-bottom, #_qf_Activity_submit-top, #_qf_Activity_refresh-top, #_qf_Activity_refresh-bottom", $content);
    }
  }

  if ($context == "form" && $tplName == "CRM/Contact/Form/Task/PDF.tpl") {
    $content = str_replace("showSaveDetails(\$('input[name=saveTemplate]', \$form)[0]);", "if (\$('input[name=saveTemplate]').length) { showSaveDetails(\$('input[name=saveTemplate]', \$form)[0]); }", $content);
  }

  $hooks = [
    new CRM_Civicase_Hook_alterContent_AddSalesOrderLineToContribution($content, $context, $tplName),
  ];

  foreach ($hooks as $hook) {
    $hook->run();
  }
}

/**
 * Implements hook_civicrm_caseEmailSubjectPatterns().
 */
function civicase_civicrm_caseEmailSubjectPatterns(&$subjectPatterns) {
  $subjectPatterns[] = '/\[[a-z0-9\s]*#([0-9a-f]{7})\]/i';
  $subjectPatterns[] = '/\[[a-z0-9\s]*#(\d+)\]/i';
}
