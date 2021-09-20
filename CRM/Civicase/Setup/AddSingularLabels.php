<?php

use CRM_Civicase_Service_CaseCategoryCustomFieldsSetting as CaseCategoryCustomFieldsSetting;

/**
 * Adds the singular label value for each case category.
 *
 * If the case category ends in an S, it will remove it.
 */
class CRM_Civicase_Setup_AddSingularLabels {

  /**
   * Adds the singular label value for each case category.
   *
   * If the case category ends in an S, it will remove it.
   */
  public function apply() {
    $caseCategoryCustomFields = new CaseCategoryCustomFieldsSetting();
    $caseTypeCategories = civicrm_api3('OptionValue', 'get', [
      'sequential' => '1',
      'option_group_id' => 'case_type_categories',
    ]);

    foreach ($caseTypeCategories['values'] as $caseTypeCategory) {
      $isLabelLastCharacterS = substr(strtolower($caseTypeCategory['label']), -1) === 's';
      $singularLabel = $isLabelLastCharacterS
        ? substr($caseTypeCategory['label'], 0, -1)
        : $caseTypeCategory['label'];

      $caseCategoryCustomFields->save($caseTypeCategory['value'], [
        'singular_label' => $singularLabel,
      ]);
    }
  }

}
