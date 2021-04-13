<?php

/**
 * Adds singular labels to case type categories.
 */
class CRM_Civicase_Upgrader_Steps_Step0015 {

  /**
   * Runs the upgrader changes.
   *
   * @return bool
   *   True when the upgrader runs successfully.
   */
  public function apply() {
    $this->addSingularLabelColumn();
    $this->addSingularLabelToCaseCategories();

    return TRUE;
  }

  /**
   * Adds the singular label column to the case category instance table.
   */
  private function addSingularLabelColumn() {
    CRM_Core_DAO::executeQuery("
      ALTER TABLE civicrm_case_category_instance
      ADD COLUMN singular_label varchar(255) character set utf8mb4 COLLATE utf8mb4_unicode_ci
        DEFAULT NULL
    ");
  }

  /**
   * Adds the singular label value for each case category.
   *
   * If the case category ends in an S, it will remove it.
   */
  private function addSingularLabelToCaseCategories () {
    $caseTypeCategories = civicrm_api3('OptionValue', 'get', [
      'sequential' => '1',
      'option_group_id' => 'case_type_categories',
    ]);

    foreach ($caseTypeCategories['values'] as $caseTypeCategory) {
      $isLabelLastCharacterS = substr(strtolower($caseTypeCategory['label']), -1) === 's';
      $singularLabel = $isLabelLastCharacterS
        ? substr($caseTypeCategory['label'], -1)
        : $caseTypeCategory['label'];

      civicrm_api3('CaseCategoryInstance', 'get', [
        'category_id' => $caseTypeCategory['id'],
        'api.CaseCategoryInstance.create' => [
          'id' => '$value.id',
          'singular_label' => $singularLabel,
        ],
      ]);
    }
  }

}
