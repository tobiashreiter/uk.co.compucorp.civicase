<?php

use CRM_Core_BAO_CustomGroup as CustomGroup;

/**
 * The base class for case category custom group processor classes.
 */
abstract class CRM_Civicase_Service_BaseCustomGroupPostProcessor {

  /**
   * Handles the saving of a custom group related to a case type category.
   *
   * @param \CRM_Core_BAO_CustomGroup $customGroup
   *   Custom group object.
   */
  abstract public function saveCustomGroupForCaseCategory(CustomGroup $customGroup);

}
