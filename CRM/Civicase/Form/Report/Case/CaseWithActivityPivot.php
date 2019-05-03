<?php

/**
 * Class CRM_Extendedreport_Form_Report_Case_CaseWithActivityPivot
 */
class CRM_Civicase_Form_Report_Case_CaseWithActivityPivot extends CRM_Civicase_Form_Report_BaseExtendedReport {
  protected $_baseTable = 'civicrm_case';
  protected $skipACL = FALSE;
  protected $_customGroupAggregates = TRUE;
  protected $_aggregatesIncludeNULL = TRUE;
  protected $_aggregatesAddTotal = TRUE;
  protected $_rollup = 'WITH ROLLUP';
  protected $_temporary = ' TEMPORARY ';
  protected $_aggregatesAddPercentage = TRUE;
  public $_drilldownReport = [];
  protected $isPivot = TRUE;
  protected $_noFields = TRUE;
  protected $_potentialCriteria = [];
  protected $tempCaseActivityTableName = '';


  public function __construct() {
    $this->_customGroupExtended['civicrm_case'] = [
      'extends' => ['Case'],
      'filters' => TRUE,
      'title' => ts('Case'),
    ];
    $this->_customGroupExtended['civicrm_activity'] = [
      'extends' => ['Activity'],
      'filters' => TRUE,
      'title' => ts('Activity'),
    ];

    $this->_columns = $this->getColumns('Case', ['fields' => FALSE])
      + $this->getColumns('Contact', array('prefix_label' => 'Case Client - ', 'group_title' => 'Case Client Contact'))
      + $this->_columns = $this->getColumns('Activity', ['fields' => FALSE]);
    $this->_columns['civicrm_case']['fields']['id']['required'] = TRUE;
    $this->_columns['civicrm_contact']['fields']['id']['required'] = TRUE;
    $this->_columns['civicrm_case']['fields']['id']['title'] = 'Case';
    $this->_columns['civicrm_contact']['fields']['gender_id']['no_display'] = TRUE;
    $this->_columns['civicrm_contact']['fields']['gender_id']['title'] = 'Gender';

    $this->_tagFilter = TRUE;
    $this->_groupFilter = TRUE;
    $this->addResultsTab();
    parent::__construct();
  }


  /**
   * {@inheritdoc}
   *
   * @return array
   */
  function fromClauses() {
    return [
      'contact_from_case',
      'activity_from_case',
    ];
  }


  /**
   * SQl query to join the to activity table from the case table
   */
  function joinActivityFromCase() {
    $caseActivityTable = $this->_caseActivityTable;
    if (!empty($this->_params['unique_cases'])) {
      $this->generateTempCaseActivityTable();
      $caseActivityTable = $this->tempCaseActivityTableName;
    }

    $this->_from .= "
      LEFT JOIN {$caseActivityTable} cca ON cca.case_id = {$this->_aliases['civicrm_case']}.id
      LEFT JOIN civicrm_activity {$this->_aliases['civicrm_activity']} ON {$this->_aliases['civicrm_activity']}.id = cca.activity_id";
  }

  /**
   * Temp table for unique case IDs with activity
   */
  protected function generateTempCaseActivityTable() {
    $tempTable = 'civicrm_unique_case_activity' . date('d_H_I') . rand(1, 10000);
    $sql = "CREATE {$this->_temporary} TABLE $tempTable
    (`case_id` INT(10) UNSIGNED NULL DEFAULT '0',
    `activity_id` INT(10) UNSIGNED NULL DEFAULT '0',
    INDEX `case_id` (`case_id`),
    INDEX `activity_id` (`activity_id`)
    )
    COLLATE='utf8_unicode_ci'
    ENGINE=HEAP;";
    CRM_Core_DAO::executeQuery($sql);
    $sql = "
      INSERT INTO $tempTable
      SELECT DISTINCT case_id, activity_id FROM civicrm_case_activity GROUP BY case_id;";
    CRM_Core_DAO::executeQuery($sql);
    $this->tempCaseActivityTableName = $tempTable;
  }

  /**
   * Add the results tab to the tabs list. Also add the unique_cases checkbox to return
   * unique case Ids when checked.
   */
  protected function addResultsTab() {
    $this->add('advcheckbox', 'unique_cases', ts('Include Only Unique Cases?'));
    $this->tabs['Results'] = [
      'title' => ts('Results'),
      'tpl' => 'Results',
      'div_label' => 'set-results',
    ];
  }
}
