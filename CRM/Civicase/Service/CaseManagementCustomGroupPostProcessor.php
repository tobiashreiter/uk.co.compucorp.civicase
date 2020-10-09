<?php

use CRM_Core_BAO_CustomGroup as CustomGroup;
use CRM_Civicase_Helper_InstanceCustomGroupPostProcess as InstanceCustomGroupPostProcess;

/**
 * Case management custom group post processor class.
 *
 * Handles events after a custom group extending a case category entity
 * is saved.
 */
class CRM_Civicase_Service_CaseManagementCustomGroupPostProcessor extends CRM_Civicase_Service_BaseCustomGroupPostProcessor {

  /**
   * Saves case type category custom groups.
   *
   * This function allows saving the Custom groups that extends a Case category
   * (that belongs to the Case Management Instance) entity to extend the Case
   * entity and to save the ID of the case category in the
   * `extends_entity_column_id` of the `custom_group` table and also to store
   * all case types for the Case category in the `extends_entity_column_value`
   * column.
   *
   * @param \CRM_Core_BAO_CustomGroup $customGroup
   *   Custom Group Object.
   * @param \CRM_Civicase_Helper_InstanceCustomGroupPostProcess $postProcessHelper
   *   Post process helper class.
   */
  public function saveCustomGroupForCaseCategory(CustomGroup $customGroup, InstanceCustomGroupPostProcess $postProcessHelper) {
    $caseTypeCategories = CRM_Civicase_Helper_CaseCategory::getCaseCategories();
    $caseTypeCategories = array_column($caseTypeCategories, 'value', 'name');

    if (empty($caseTypeCategories[$customGroup->extends])) {
      return;
    }

    $caseTypeIds = $postProcessHelper->getCaseTypeIdsForCaseCategory($customGroup, $caseTypeCategories[$customGroup->extends]);
    $ids = 'null';
    if (!empty($caseTypeIds)) {
      $ids = CRM_Core_DAO::VALUE_SEPARATOR . implode(CRM_Core_DAO::VALUE_SEPARATOR, $caseTypeIds) . CRM_Core_DAO::VALUE_SEPARATOR;
    }

    $customGroup->extends_entity_column_value = $ids;
    $customGroup->extends_entity_column_id = $caseTypeCategories[$customGroup->extends];
    $customGroup->extends = 'Case';
    $customGroup->save();
  }

}
