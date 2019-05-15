<?php

class CRM_Civicase_Form_Report_BaseExtendedReport extends CRM_Civicase_Form_Report_ExtendedReport {

  public $aggregateDateFields;

  public $dateSqlGrouping = [
    'month' => "%Y-%m",
    'year' => "%Y"
  ];

  /**
   *  Add the fields to select the aggregate fields to the report.
   *
   * This function is overridden because of a bug that does not allow the custom fields to
   * appear in the Filters tab in the base class.
   */
  protected function addAggregateSelectorsToForm() {
    if (!$this->isPivot) {
      return;
    }
    $aggregateColumnHeaderFields = $this->getAggregateColumnFields();
    $aggregateRowHeaderFields = $this->getAggregateRowFields();

    foreach ($this->_customGroupExtended as $key => $groupSpec) {
      $customDAOs = $this->getCustomDataDAOs($groupSpec['extends']);
      foreach ($customDAOs as $customField) {
        $tableKey = $customField['prefix'] . $customField['table_name'];
        $prefix = $customField['prefix'];
        $fieldName = 'custom_' . ($prefix ? $prefix . '_' : '') . $customField['id'];
        $this->addCustomTableToColumns($customField, $customField['table_name'], $prefix, $customField['prefix_label'], $tableKey);
        $this->_columns[$tableKey]['metadata'][$fieldName] = $this->getCustomFieldMetadata($customField, $customField['prefix_label']);
        if (!empty($groupSpec['filters'])) {
          $this->_columns[$tableKey]['metadata'][$fieldName]['is_filters'] = TRUE;
          $this->_columns[$tableKey]['metadata'][$fieldName]['extends_table'] = $this->_columns[$tableKey]['extends_table'];
          $this->_columns[$tableKey]['filters'][$fieldName] = $this->_columns[$tableKey]['metadata'][$fieldName];
        }
        $this->metaData['metadata'][$fieldName] = $this->_columns[$tableKey]['metadata'][$fieldName];
        $this->metaData['metadata'][$fieldName]['is_aggregate_columns'] = TRUE;
        $this->metaData['metadata'][$fieldName]['table_alias'] = $this->_columns[$tableKey]['alias'];
        $this->metaData['aggregate_columns'][$fieldName] = $this->metaData['metadata'][$fieldName];
        $this->metaData['filters'][$fieldName] = $this->metaData['metadata'][$fieldName];
        $aggregateRowHeaderFields[$prefix . 'custom_' . $customField['id']] = $customField['prefix_label'] . $customField['label'];
        if (in_array($customField['html_type'], ['Select', 'CheckBox'])) {
          $aggregateColumnHeaderFields[$prefix . 'custom_' . $customField['id']] = $customField['prefix_label'] . $customField['label'];
        }
      }

    }

    $this->addSelect(
      'aggregate_column_headers',
      [
        'entity' => '',
        'option_url' => NULL,
        'label' => ts('Aggregate Report Column Headers'),
        'options' => $aggregateColumnHeaderFields,
        'id' => 'aggregate_column_headers',
        'placeholder' => ts('- select -'),
        'class' => 'huge',
      ],
      FALSE
    );
    $this->addSelect(
      'aggregate_row_headers',
      [
        'entity' => '',
        'option_url' => NULL,
        'label' => ts('Row Fields'),
        'options' => $aggregateRowHeaderFields,
        'id' => 'aggregate_row_headers',
        'placeholder' => ts('- select -'),
        'class' => 'huge',
      ],
      FALSE
    );

    $this->addSelect(
      'aggregate_column_date_grouping',
      [
        'entity' => '',
        'option_url' => NULL,
        'label' => ts('Date Grouping'),
        'options' => ['month' => 'Month', 'year' => 'Year'],
        'id' => 'aggregate_column_date_grouping',
        'placeholder' => ts('- select -'),
      ],
      FALSE
    );

    $this->_columns[$this->_baseTable]['fields']['include_null'] = [
      'title' => 'Show column for unknown',
      'pseudofield' => TRUE,
      'default' => TRUE,
    ];
    $this->tabs['Aggregate'] = [
      'title' => ts('Pivot table'),
      'tpl' => 'Aggregate',
      'div_label' => 'set-aggregate',
    ];

    $this->assign('aggregateDateFields', json_encode(array_flip($this->aggregateDateFields)));
    $this->assignTabs();
  }

  /**
   * This function is overridden because of a bug that selects wrong data for custom fields
   * extending an entity when there are multiple instances of the Entity in columns.
   * For example, there are more than one Contact Entity columns, for Case client contact, and also
   * Case roles contacts, the custom field value for the other Contact custom fields is selected
   * wrongly because the db alias of the first Contact entity is used in all case. This is fixed
   * by using the table key to form the alias rather than the original table name which is same for
   * all Contact entity data.
   *
   * @param string $field
   * @param string $prefixLabel
   * @param string $prefix
   *
   * @return mixed
   */
  protected function getCustomFieldMetadata($field, $prefixLabel, $prefix = '') {
    $field = array_merge($field, [
      'name' => $field['column_name'],
      'title' => $prefixLabel . $field['label'],
      'dataType' => $field['data_type'],
      'htmlType' => $field['html_type'],
      'operatorType' => $this->getOperatorType($this->getFieldType($field), [], []),
      'is_fields' => TRUE,
      'is_filters' => TRUE,
      'is_group_bys' => FALSE,
      'is_order_bys' => FALSE,
      'is_join_filters' => FALSE,
      'type' => $this->getFieldType($field),
      'dbAlias' => $prefix . $field['table_key'] . '.' . $field['column_name'],
      'alias' => $prefix . $field['table_name'] . '_' . 'custom_' . $field['id'],
    ]);
    $field['is_aggregate_columns'] = in_array($field['html_type'], ['Select', 'Radio']);

    if (!empty($field['option_group_id'])) {
      if (in_array($field['html_type'], [
        'Multi-Select',
        'AdvMulti-Select',
        'CheckBox',
      ])) {
        $field['operatorType'] = CRM_Report_Form::OP_MULTISELECT_SEPARATOR;
      }
      else {
        $field['operatorType'] = CRM_Report_Form::OP_MULTISELECT;
      }

      $ogDAO = CRM_Core_DAO::executeQuery("SELECT ov.value, ov.label FROM civicrm_option_value ov WHERE ov.option_group_id = %1 ORDER BY ov.weight", [
        1 => [$field['option_group_id'], 'Integer'],
      ]);
      while ($ogDAO->fetch()) {
        $field['options'][$ogDAO->value] = $ogDAO->label;
      }
    }

    if ($field['type'] === CRM_Utils_Type::T_BOOLEAN) {
      $field['options'] = [
        '' => ts('- select -'),
        1 => ts('Yes'),
        0 => ts('No'),
      ];
    }
    return $field;
  }

  /**
   * This function is overridden because there is an issue with the naming for the
   * custom group panel labels on the filter section in the UI. The group title for the
   * custom groups can not be passed in when defining the fields hence the need to override
   * this function.
   *
   * @param string $field
   * @param string $currentTable
   * @param string $prefix
   * @param string $prefixLabel
   * @param string $tableKey
   */
  protected function addCustomTableToColumns($field, $currentTable, $prefix, $prefixLabel, $tableKey) {
    $entity = $field['extends'];
    if (in_array($entity, ['Individual', 'Organization', 'Household'])) {
      $entity = 'Contact';
    }
    if (!isset($this->_columns[$tableKey])) {
      $this->_columns[$tableKey]['extends'] = $field['extends'];
      $this->_columns[$tableKey]['grouping'] = $prefix . $field['table_name'];
      $this->_columns[$tableKey]['group_title'] = $field['table_label'];
      $this->_columns[$tableKey]['name'] = $field['table_name'];
      $this->_columns[$tableKey]['fields'] = [];
      $this->_columns[$tableKey]['filters'] = [];
      $this->_columns[$tableKey]['join_filters'] = [];
      $this->_columns[$tableKey]['group_bys'] = [];
      $this->_columns[$tableKey]['order_bys'] = [];
      $this->_columns[$tableKey]['aggregates'] = [];
      $this->_columns[$tableKey]['prefix'] = $prefix;
      $this->_columns[$tableKey]['table_name'] = $currentTable;
      $this->_columns[$tableKey]['alias'] = $prefix . $currentTable;
      $this->_columns[$tableKey]['extends_table'] = $prefix . CRM_Core_DAO_AllCoreTables::getTableForClass(CRM_Core_DAO_AllCoreTables::getFullName($entity));
    }
  }

  /**
   * This function is overridden because of custom JOINs for the
   * Case activity pivot report that are not available in base class.
   *
   * @return array
   */
  public function getAvailableJoins() {
    $availableJoins = parent::getAvailableJoins();

    $joins = [
      'relationship_from_case' => [
        'callback' => 'joinRelationshipFromCase',
      ],
      'case_role_contact' => [
        'callback' => 'joinCaseRolesContact',
      ]
    ];

    return array_merge($availableJoins, $joins);
  }

  public function alterRollupRows(&$rows) {
    if (count($rows) === 1) {
      // If the report only returns one row there is no rollup.
      return;
    }
    array_walk($rows, [$this, 'replaceNullRowValues']);
    $groupBys = array_reverse(array_fill_keys(array_keys($this->_groupByArray), NULL));
    $firstRow = reset($rows);
    foreach ($groupBys as $field => $groupBy) {
      $fieldKey = isset($firstRow[$field]) ? $field : str_replace([
        '_YEAR',
        '_MONTH',
      ], '_start', $field);
      if (isset($firstRow[$fieldKey])) {
        unset($groupBys[$field]);
        $groupBys[$fieldKey] = $firstRow[$fieldKey];
      }
    }
    $groupByLabels = array_keys($groupBys);

    $altered = [];
    $fieldsToUnSetForSubtotalLines = [];
    //on this first round we'll get a list of keys that are not groupbys or stats
    foreach (array_keys($firstRow) as $rowField) {
      if (!array_key_exists($rowField, $groupBys) && substr($rowField, -4) != '_sum' && !substr($rowField, -7) != '_count') {
        $fieldsToUnSetForSubtotalLines[] = $rowField;
      }
    }

    $statLayers = count($this->_groupByArray);

    //I don't know that this precaution is required?          $this->fixSubTotalDisplay($rows[$rowNum], $this->_statFields);
    if (count($this->_statFields) == 0) {
      return;
    }

    foreach (array_keys($rows) as $rowNumber) {
      $nextRow = CRM_Utils_Array::value($rowNumber + 1, $rows);
      if ($nextRow === NULL && empty($this->rollupRow)) {
        $this->updateRollupRow($rows[$rowNumber], $fieldsToUnSetForSubtotalLines);
      }
      else {
        $this->alterRowForRollup($rows[$rowNumber], $nextRow, $groupBys, $rowNumber, $statLayers, $groupByLabels, $altered, $fieldsToUnSetForSubtotalLines);
      }
    }
  }

  /**
   * @param $row
   * @param $nextRow
   * @param $groupBys
   * @param $rowNumber
   * @param $statLayers
   *
   * @param $groupByLabels
   * @param $altered
   * @param $fieldsToUnSetForSubtotalLines
   *
   * @return mixed
   */
  private function alterRowForRollup(&$row, $nextRow, &$groupBys, $rowNumber, $statLayers, $groupByLabels, $altered, $fieldsToUnSetForSubtotalLines) {
    foreach ($groupBys as $field => $groupBy) {
      if (($rowNumber + 1) < $statLayers) {
        continue;
      }
      if (empty($row[$field]) && empty($row['is_rollup'])) {
        $valueIndex = array_search($field, $groupBys) + 1;
        if (!isset($groupByLabels[$valueIndex])) {
          return;
        }
        $groupedValue = $groupByLabels[$valueIndex];
        if (!($nextRow) || $nextRow[$groupedValue] != $row[$groupedValue]) {
          //we set altered because we are started from the lowest grouping & working up & if both have changed only want to act on the lowest
          //(I think)
          $altered[$rowNumber] = TRUE;
//          $row[$groupedValue] = "<span class= 'report-label'> {$row[$groupedValue]} (Subtotal)</span> ";
          $this->updateRollupRow($row, $fieldsToUnSetForSubtotalLines);
        }
      }
      $groupBys[$field] = $row[$field];
    }
  }
  /**
   * Replace NULL row values with the 'NULL' keyword
   */
  private function replaceNullRowValues(&$row, $key) {
    foreach ($row as $field => $value) {
      if (is_null($value)) {
        $row[$field] = 'NULL';
      }
    }
  }

  /**
   * Add Select for pivot chart style report
   *
   * @param string $fieldName
   * @param string $dbAlias
   * @param array $spec
   *
   * @throws Exception
   */
  function addColumnAggregateSelect($fieldName, $dbAlias, $spec) {
    $me = 'ddd';
    if (empty($fieldName)) {
      $this->addAggregateTotal($fieldName, $dbAlias);
      return;
    }
    $spec['dbAlias'] = $dbAlias;
    $options = $this->getCustomFieldOptions($spec);

    if (!empty($this->_params[$fieldName . '_value']) && CRM_Utils_Array::value($fieldName . '_op', $this->_params) == 'in') {
      $options['values'] = array_intersect_key($options, array_flip($this->_params[$fieldName . '_value']));
    }

    $filterSpec = [
      'field' => ['name' => $fieldName],
      'table' => ['alias' => $spec['table_name']],
    ];

    if ($this->getFilterFieldValue($spec)) {
      // for now we will literally just handle IN
      if ($filterSpec['field']['op'] == 'in') {
        $options = array_intersect_key($options, array_flip($filterSpec['field']['value']));
        $this->_aggregatesIncludeNULL = FALSE;
      }
    }

    foreach ($options as $optionValue => $optionLabel) {
      $fieldAlias = str_replace([
        '-',
        '+',
        '\/',
        '/',
        ')',
        '(',
      ], '_', "{$fieldName}_" . strtolower(str_replace(' ', '', $optionValue)));

      // htmlType is set for custom data and tells us the field will be stored using hex(01) separators.
      if (!empty($spec['htmlType']) && in_array($spec['htmlType'], [
          'CheckBox',
          'MultiSelect',
        ])
      ) {
        $this->_select .= " , SUM( CASE WHEN {$dbAlias} LIKE '%" . CRM_Core_DAO::VALUE_SEPARATOR . $optionValue . CRM_Core_DAO::VALUE_SEPARATOR . "%' THEN 1 ELSE 0 END ) AS $fieldAlias ";
      }
      else if (!empty($spec['html']['type']) && $spec['html']['type'] == 'Select Date') {
        $dateGrouping = $this->_params['aggregate_column_date_grouping'];
        $this->_select .= " , SUM( CASE DATE_FORMAT({$dbAlias}, '{$this->dateSqlGrouping[$dateGrouping]}') WHEN '{$optionValue}' THEN 1 ELSE 0 END ) AS $fieldAlias ";
      }
      else {
        $this->_select .= " , SUM( CASE {$dbAlias} WHEN '{$optionValue}' THEN 1 ELSE 0 END ) AS $fieldAlias ";
      }
      $this->_columnHeaders[$fieldAlias] = [
        'title' => !empty($optionLabel) ? $optionLabel : 'NULL',
        'type' => CRM_Utils_Type::T_INT,
      ];
      $this->_statFields[] = $fieldAlias;
    }
    if ($this->_aggregatesIncludeNULL && !empty($this->_params['fields']['include_null'])) {
      $fieldAlias = "{$fieldName}_null";
      $this->_columnHeaders[$fieldAlias] = [
        'title' => ts('Unknown'),
        'type' => CRM_Utils_Type::T_INT,
      ];
      $this->_select .= " , SUM( IF (({$dbAlias} IS NULL OR {$dbAlias} = ''), 1, 0)) AS $fieldAlias ";
      $this->_statFields[] = $fieldAlias;
    }
    if ($this->_aggregatesAddTotal) {
      $this->addAggregateTotal($fieldName, $dbAlias);
    }
  }

  /**
   * @param $spec
   *
   * @return array
   * @throws \Exception
   */
  protected function getCustomFieldOptions($spec) {
    $options = [];
    if (!empty($spec['options'])) {
      return $spec['options'];
    }

    if ($spec['type'] == CRM_Report_Form::OP_DATE) {
      return $this->getDateColumnOptions($spec);
    }

    // Data type is set for custom fields but not core fields.
    if (CRM_Utils_Array::value('data_type', $spec) == 'Boolean') {
      $options = [
        'values' => [
          0 => ['label' => 'No', 'value' => 0],
          1 => ['label' => 'Yes', 'value' => 1],
        ],
      ];
    }
    elseif (!empty($spec['options'])) {
      foreach ($spec['options'] as $option => $label) {
        $options['values'][$option] = [
          'label' => $label,
          'value' => $option,
        ];
      }
    }
    else {
      if (empty($spec['option_group_id'])) {
        throw new Exception('currently column headers need to be radio or select');
      }
      $options = civicrm_api('option_value', 'get', [
        'version' => 3,
        'options' => ['limit' => 50,],
        'option_group_id' => $spec['option_group_id'],
      ]);
    }
    return $options['values'];
  }

  public function getDateColumnOptions($spec) {
    $this->from();
    $this->where();
    $dateGrouping = $this->_params['aggregate_column_date_grouping'];
    $select = "SELECT DISTINCT DATE_FORMAT({$spec['dbAlias']}, '{$this->dateSqlGrouping[$dateGrouping]}') as date_grouping";
    $sql = "{$select} {$this->_from} {$this->_where} ORDER BY date_grouping ASC";
    if (!$this->_rollup) {
      $sql .= $this->_limit;
    }

    $result = CRM_Core_DAO::executeQuery($sql);
    $options = [];
    while ($result->fetch()) {
      $options[$result->date_grouping] = $result->date_grouping;
    }

    return $options;
  }

  /**
   * @param $fieldName
   */
  function addAggregateTotal($fieldName, $dbAlias) {
    $fieldAlias = "{$fieldName}_total";
    $this->_columnHeaders[$fieldAlias] = [
      'title' => ts('Total'),
      'type' => CRM_Utils_Type::T_INT,
    ];
    $this->_select .= " , SUM( IF ({$dbAlias}, 1, 0)) AS $fieldAlias ";
    $this->_statFields[] = $fieldAlias;
  }

  protected function buildColumns($specs, $tableName, $daoName = NULL, $tableAlias = NULL, $defaults = [], $options = []) {

    if (!$tableAlias) {
      $tableAlias = str_replace('civicrm_', '', $tableName);
    }
    $types = ['filters', 'group_bys', 'order_bys', 'join_filters', 'aggregate_columns', 'aggregate_rows'];
    $columns = [$tableName => array_fill_keys($types, [])];
    if (!empty($daoName)) {
      $columns[$tableName]['bao'] = $daoName;
    }
    $columns[$tableName]['alias'] = $tableAlias;
    $exportableFields = $this->getMetadataForFields(['dao' => $daoName]);

    foreach ($specs as $specName => $spec) {
      $spec['table_key'] = $tableName;
      unset($spec['default']);
      if (empty($spec['name'])) {
        $spec['name'] = $specName;
      }
      if (empty($spec['dbAlias'])) {
        $spec['dbAlias'] = $tableAlias . '.' . $spec['name'];
      }
      $daoSpec = CRM_Utils_Array::value($spec['name'], $exportableFields, CRM_Utils_Array::value($tableAlias . '_' . $spec['name'], $exportableFields, []));
      $spec = array_merge($daoSpec, $spec);
      if (!isset($columns[$tableName]['table_name']) && isset($spec['table_name'])) {
        $columns[$tableName]['table_name'] = $spec['table_name'];
      }

      if (!isset($spec['operatorType'])) {
        $spec['operatorType'] = $this->getOperatorType($spec['type'], $spec);
      }
      foreach (array_merge($types, ['fields']) as $type) {
        if (isset($options[$type]) && !empty($spec['is_' . $type])) {
          // Options can change TRUE to FALSE for a field, but not vice versa.
          $spec['is_' . $type] = $options[$type];
        }
        if (!isset($spec['is_' . $type]))    {
          $spec['is_' . $type] = FALSE;
        }
      }

      $fieldAlias = (empty($options['no_field_disambiguation']) ? $tableAlias . '_' : '') . $specName;
      $spec['alias'] = $tableName . '_' . $fieldAlias;
      if ($this->isPivot && (!empty($spec['options']) || $spec['operatorType'] == CRM_Report_Form::OP_DATE)) {
        $spec['is_aggregate_columns'] = TRUE;
        $spec['is_aggregate_rows'] = TRUE;

        if ($spec['operatorType'] == CRM_Report_Form::OP_DATE) {
          $this->aggregateDateFields[] = $fieldAlias;
        }
      }
      $columns[$tableName]['metadata'][$fieldAlias] = $spec;
      $columns[$tableName]['fields'][$fieldAlias] = $spec;
      if (isset($defaults['fields_defaults']) && in_array($spec['name'], $defaults['fields_defaults'])) {
        $columns[$tableName]['metadata'][$fieldAlias]['is_fields_default'] = TRUE;
      }

      if (empty($spec['is_fields']) || (isset($options['fields_excluded']) && in_array($specName, $options['fields_excluded']))) {
        $columns[$tableName]['fields'][$fieldAlias]['no_display'] = TRUE;
      }

      if (!empty($spec['is_filters']) && !empty($spec['statistics']) && !empty($options) && !empty($options['group_by'])) {
        foreach ($spec['statistics'] as $statisticName => $statisticLabel) {
          $columns[$tableName]['filters'][$fieldAlias . '_' . $statisticName] = array_merge($spec, [
            'title' => E::ts('Aggregate filter : ') . $statisticLabel,
            'having' => TRUE,
            'dbAlias' => $tableName . '_' . $fieldAlias . '_' . $statisticName,
            'selectAlias' => "{$statisticName}({$tableAlias}.{$spec['name']})",
            'is_fields' => FALSE,
            'is_aggregate_field_for' => $fieldAlias,
          ]);
          $columns[$tableName]['metadata'][$fieldAlias . '_' . $statisticName] = $columns[$tableName]['filters'][$fieldAlias . '_' . $statisticName];
        }
      }

      foreach ($types as $type) {
        if (!empty($spec['is_' . $type])) {
          if ($type === 'join_filters') {
            $fieldAlias = 'join__' . $fieldAlias;
          }
          $columns[$tableName][$type][$fieldAlias] = $spec;
          if (isset($defaults[$type . '_defaults']) && isset($defaults[$type . '_defaults'][$spec['name']])) {
            $columns[$tableName]['metadata'][$fieldAlias]['default'] = $defaults[$type . '_defaults'][$spec['name']];
          }
        }
      }
    }
    $columns[$tableName]['prefix'] = isset($options['prefix']) ? $options['prefix'] : '';
    $columns[$tableName]['prefix_label'] = isset($options['prefix_label']) ? $options['prefix_label'] : '';
    if (isset($options['group_title'])) {
      $groupTitle = $options['group_title'];
    }
    else {

      // We can make one up but it won't be translated....
      $groupTitle = ucfirst(str_replace('_', ' ', str_replace('civicrm_', '', $tableName)));
    }
    $columns[$tableName]['group_title'] = $groupTitle;
    return $columns;
  }

  public function formatDisplay(&$rows, $pager = TRUE) {
    // set pager based on if any limit was applied in the query.
    if ($pager) {
      $this->setPager();
    }

    // unset columns not to be displayed.
    foreach ($this->_columnHeaders as $key => $value) {
      if (!empty($value['no_display'])) {
        unset($this->_columnHeaders[$key]);
      }
    }

    // unset columns not to be displayed.
    if (!empty($rows)) {
      foreach ($this->_noDisplay as $noDisplayField) {
        foreach ($rows as $rowNum => $row) {
          unset($this->_columnHeaders[$noDisplayField]);
        }
      }
    }

    // build array of section totals
    $this->sectionTotals();

    end($rows);         // move the internal pointer to the end of the array
    $rollup_row_key = key($rows);
    reset($rows);
    $rollup_row = $rows[$rollup_row_key];
    unset($rows[$rollup_row_key]);
    $array_keys = array_keys($rollup_row);
    $final_rollup = [];
    foreach ($array_keys as $key) {
      $final_rollup[$key] =  array_sum(array_column($rows, "$key"));
    }

    $rows[$rollup_row_key] =  $final_rollup;
    // process grand-total row
    $this->grandTotal($rows);

    // use this method for formatting rows for display purpose.
    $this->alterDisplay($rows);
    CRM_Utils_Hook::alterReportVar('rows', $rows, $this);

    // use this method for formatting custom rows for display purpose.
    $this->alterCustomDataDisplay($rows);
  }
}
