<?php

/**
 * Case Instance class.
 */
class CRM_Civicase_Service_CaseInstance {

  /**
   * Assigns instance to case type categories without an instance.
   *
   * Assigns `case_management` instance to the existing case type categories
   * which does not have instance assigned.
   */
  public static function assignInstanceForExistingCaseCategories() {
    $caseTypeCategories = civicrm_api3('OptionValue', 'get', [
      'sequential' => 1,
      'option_group_id' => 'case_type_categories',
    ])['values'];

    $instances = civicrm_api3('CaseCategoryInstance', 'get', [
      'sequential' => 1,
    ])['values'];

    foreach ($caseTypeCategories as $caseTypeCategory) {
      $instanceRecord = NULL;

      foreach ($instances as $instance) {
        if ($instance['category_id'] == $caseTypeCategory['value']) {
          $instanceRecord = $instance;
          break;
        }
      }

      if (!$instanceRecord) {
        civicrm_api3('CaseCategoryInstance', 'create', [
          'category_id' => $caseTypeCategory['value'],
          'instance_id' => 'case_management',
        ]);
      }
    }
  }

}
