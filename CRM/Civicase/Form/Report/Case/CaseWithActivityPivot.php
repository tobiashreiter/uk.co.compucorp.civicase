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
}
