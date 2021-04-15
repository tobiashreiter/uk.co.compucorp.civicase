<?php

use CRM_Civicase_Service_CaseCategoryCustomFieldsSetting as CaseCategoryCustomFieldsSetting;

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
    $this->addSingularLabelToCaseCategories();

    return TRUE;
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

      CaseCategoryCustomFieldsSetting::save($caseTypeCategory['value'], [
        'singular_label' => $singularLabel,
      ]);
    }
  }

}
