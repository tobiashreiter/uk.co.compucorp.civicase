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
  protected $tempCaseRelationshipTableName;
  protected $caseRoleContactMetaData = [];


  public function __construct() {
    $this->setCaseRolesContactMetaData();
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

    $caseColumns = $this->getColumns('Case', ['fields' => FALSE]);
    $caseClientContactColumns = $this->getColumns('Contact', ['prefix_label' => 'Case Client - ', 'group_title' => 'Case Client Contact']);
    $activityColumns = $this->_columns = $this->getColumns('Activity', ['fields' => FALSE]);
    $caseRolesContactColumns = $this->getCaseRolesContactColumns();

    $this->_columns = $caseColumns + $caseClientContactColumns + $activityColumns + $caseRolesContactColumns;
    $this->_columns['civicrm_case']['fields']['id']['required'] = TRUE;
    $this->_columns['civicrm_contact']['fields']['id']['required'] = TRUE;
    $this->_columns['civicrm_case']['fields']['id']['title'] = 'Case';
    $this->_columns['civicrm_contact']['fields']['gender_id']['no_display'] = TRUE;
    $this->_columns['civicrm_contact']['fields']['gender_id']['title'] = 'Gender';

    $this->_tagFilter = TRUE;
    $this->_groupFilter = TRUE;
    $this->addResultsTab();
    parent::__construct();
    $this->addAdditionFilterFields();
  }


  /**
   * {@inheritdoc}
   *
   * @return array
   */
  public function fromClauses() {
    return [
      'contact_from_case',
      'activity_from_case',
      'relationship_from_case',
      'case_role_contact'
    ];
  }

  /**
   * SQL condition to JOIN to the relationship table.
   * It takes into consideration active relationships and only
   * joins to a relationship that is still active.
   *
   * The as at date parameter when present will only join to case roles that with the as at date
   * between the case roles start and end dates.
   */
  public function joinRelationshipFromCase() {
    $date = !empty($this->_params['as_at_date']) ? $this->_params['as_at_date'] :date('Y-m-d');
    $activeStatus = !empty($this->_params['as_at_date']) ? "0, 1" : "1";
    $this->_from .= "
      LEFT JOIN civicrm_relationship crt 
      ON (
        {$this->_aliases['civicrm_case']}.id = crt.case_id AND
        {$this->_aliases['civicrm_contact']}.id = crt.contact_id_a AND
        crt.is_active IN({$activeStatus}) AND
        (crt.start_date IS NULL OR crt.start_date <= '{$date}') AND 
        (crt.end_date IS NULL OR crt.end_date >= '{$date}')
       )";
  }

  /**
   * SQL query condition to JOIN to the contact table for each of
   * the case roles contacts based on the relationship the role
   * has with the case client.
   */
  protected function joinCaseRolesContact() {
    foreach ($this->caseRoleContactMetaData as $data) {
      $tableAlias = $data['table_prefix'].'civicrm_contact';
      $this->_from .= "
      LEFT JOIN civicrm_contact $tableAlias 
      ON (
         crt.contact_id_b = {$tableAlias}.id AND
         crt.relationship_type_id = {$data['relationship_type_id']}
      )";
    }
  }

  /**
   * Adds some meta information for Case Roles contacts for case types.
   * This information will be used to build the columns and add the tables
   * needed to Join to the contact records for the contacts having these case role
   * relationship with the case client.
   */
  protected function setCaseRolesContactMetaData() {
    $result = civicrm_api3('CaseType', 'get', [
      'sequential' => 1,
      'return' => ['definition', 'id'],
    ]);

    $caseRoleData = [];
    foreach ($result['values'] as $value) {
      if (empty($value['definition']['caseRoles'])) {
        continue;
      }

      $caseRoles = $value['definition']['caseRoles'];
      foreach ($caseRoles as $caseRole) {
        $data = civicrm_api3('RelationshipType', 'getsingle', [
          'label_b_a' => $caseRole['name'],
        ]);
        $tablePrefix = $this->getDbPrefixFromRoleName($caseRole['name']);
        $caseRoleData[$tablePrefix] = [
          'relationship_type_id' => $data['id'],
          'relationship_name' => $caseRole['name'],
          'table_prefix' => $tablePrefix
        ];
      }
    }

    $this->caseRoleContactMetaData = $caseRoleData;
  }

  /**
   * Add the results tab to the tabs list. Also add the unique_cases checkbox to return
   * unique case Ids when checked.
   */
  protected function addResultsTab() {
    $this->tabs['Results'] = [
      'title' => ts('Results'),
      'tpl' => 'Results',
      'div_label' => 'set-results',
    ];
  }

  /**
   * Adds additional filter fields
   */
  protected function addAdditionFilterFields() {
    $this->add(
      'datepicker',
      'as_at_date',
      ts('As At Date'),
      ['size' => 35],
      FALSE,
      ['time' => FALSE]
    );
  }

  /**
   * Returns the Db prefix that will be used for Case Roles contact table.
   *
   * @param string $roleName
   *
   * @return string
   */
  private function getDbPrefixFromRoleName($roleName) {
    $stringArray = explode(' ', $roleName);

    $prefix = '';
    foreach ($stringArray as $value) {
      $prefix .= strtolower($value)."_";
    }

    return $prefix;
  }

  /**
   * Returns the contact columns meta data for the case roles for all
   * case types. This allows to join to the contact table to get contact
   * and custom field information for contacts having relationships with
   * a case client for.
   *
   * Each case role data is added once and is not duplicated.
   *
   * @return array
   */
  private function getCaseRolesContactColumns() {
    $contactColumns = [];
    foreach($this->caseRoleContactMetaData as $data) {
      $contactColumns += $this->getColumns(
        'Contact',
        [
          'prefix_label' => "{$data['relationship_name']} - ",
          'group_title' => "Contacts",
          'prefix' => $data['table_prefix'],
        ]
      );
    }

    return $contactColumns;
  }

  /**
   * Function that allows additional filter fields provided by this class to be added to the
   * where clause for the report.
   */
  protected function processAdditionalFilters() {
    if (!empty($this->_params['as_at_date'])) {
      $asAtDate = $this->_params['as_at_date'];
      $this->_whereClauses[] =
        " {$this->_aliases['civicrm_case']}.start_date <= '{$asAtDate}' AND
         ({$this->_aliases['civicrm_case']}.end_date >= '{$asAtDate}' OR {$this->_aliases['civicrm_case']}.end_date IS NULL) ";
    }
  }

  /**
   * Returns additional filter fields provided by this report class.
   *
   * @return array
   */
  protected function getAdditionalFilterFields() {
    $fields = [
      'as_at_date' => [
        'label' => 'As At Date'
      ]
    ];

    return $fields;
  }
}
