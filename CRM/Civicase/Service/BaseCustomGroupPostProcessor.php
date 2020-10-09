<?php

use CRM_Core_BAO_CustomGroup as CustomGroup;
use CRM_Civicase_Helper_InstanceCustomGroupPostProcess as InstanceCustomGroupPostProcess;

/**
 * The base class for case category custom group processor classes.
 */
abstract class CRM_Civicase_Service_BaseCustomGroupPostProcessor {

  /**
   * Handles the saving of a custom group related to a case type category.
   *
   * @param \CRM_Core_BAO_CustomGroup $customGroup
   *   Custom group object.
   * @param \CRM_Civicase_Helper_InstanceCustomGroupPostProcess $postProcessHelper
   *   Custom group instance helper.
   */
  abstract public function saveCustomGroupForCaseCategory(CustomGroup $customGroup, InstanceCustomGroupPostProcess $postProcessHelper);

}
