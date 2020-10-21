<?php

use CRM_Civicase_BAO_CaseCategoryInstance as CaseCategoryInstance;

/**
 * Case Instance class.
 */
class CRM_Civicase_Service_CaseInstance {

  /**
   * Get case categories instances.
   *
   * @param string $instanceName
   *   Instance ID.
   */
  public static function getCaseCategoryInstances($instanceName = NULL) {
    $caseCategoryInstances = [];
    $caseCategoryInstance = new CaseCategoryInstance();

    if ($instanceName) {
      $instanceId = civicrm_api3('OptionValue', 'get', [
        'sequential' => 1,
        'option_group_id' => 'case_category_instance_type',
        'name' => $instanceName,
      ])['values'][0]['value'];

      $caseCategoryInstance->instance_id = $instanceId;
    }

    $caseCategoryInstance->find();

    while ($caseCategoryInstance->fetch()) {
      $caseCategoryInstances[$caseCategoryInstance->id] = clone $caseCategoryInstance;
    }

    return $caseCategoryInstances;
  }

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

    $instances = CRM_Civicase_Service_CaseInstance::getCaseCategoryInstances();

    foreach ($caseTypeCategories as $caseTypeCategory) {
      $instanceRecord = NULL;

      foreach ($instances as $instance) {
        if ($instance->category_id == $caseTypeCategory['value']) {
          $instanceRecord = $instance;
          break;
        }
      }

      if (!$instanceRecord) {
        $instanceId = civicrm_api3('OptionValue', 'get', [
          'sequential' => 1,
          'option_group_id' => 'case_category_instance_type',
          'name' => 'case_management',
        ])['values'][0]['value'];

        CRM_Civicase_Service_CaseInstance::createInstanceTypeFor(
          $caseTypeCategory['value'],
          $instanceId
        );
      }
    }
  }

  /**
   * Creates instance for the given case type category.
   *
   * @param mixed $categoryValue
   *   Case category value.
   * @param mixed $instanceId
   *   Instance ID.
   */
  public static function createInstanceTypeFor($categoryValue, $instanceId) {
    $caseCategoryInstance = new CaseCategoryInstance();
    $caseCategoryInstance->instance_id = $instanceId;
    $caseCategoryInstance->category_id = $categoryValue;

    $caseCategoryInstance->save();
  }

}
