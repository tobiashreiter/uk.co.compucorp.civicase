<?php

/**
 * The base class for case category case type processor classes.
 */
abstract class CRM_Civicase_Service_BaseCaseTypePostProcessor {

  /**
   * This function updates the custom groups for the case type on create.
   *
   * The case type when created need to be added to the custom group
   * extending the Case entity for the case category of the case type.
   *
   * @param int $caseTypeId
   *   Custom group object..
   */
  abstract public function processCaseTypeCustomGroupsOnCreate($caseTypeId);

  /**
   * This function updates the custom groups for the case type on update.
   *
   * The case type when created need to be added to the custom group extending
   * the Case entity for the case category of the case type and also needs to be
   * removed from custom groups extending the Case entity for case category that
   * is not same as the case type (For the case when the case category of case
   * type is updated to another).
   *
   * @param int $caseTypeId
   *   Custom group object.
   */
  abstract public function processCaseTypeCustomGroupsOnUpdate($caseTypeId);

}
