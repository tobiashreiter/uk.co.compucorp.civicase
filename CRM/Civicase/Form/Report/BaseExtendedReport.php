<?php

class CRM_Civicase_Form_Report_BaseExtendedReport extends CRM_Civicase_Form_Report_ExtendedReport {

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
}
