<?php

/**
 * CRM_Civicase_Hook_Helper_CaseTypeCategory class.
 */
class CRM_Civicase_Hook_Helper_CaseTypeCategory {

  /**
   * Checks if the case type category is valid or not.
   *
   * @param string $caseCategoryName
   *   Category Name.
   *
   * @return bool
   *   return value.
   */
  public static function isValidCategory($caseCategoryName) {
    if ($caseCategoryName == 'cases') {
      return TRUE;
    }

    $caseCategoryOptions = CRM_Case_BAO_CaseType::buildOptions('case_type_category', 'validate');
    $caseCategoryOptions = array_map('strtolower', $caseCategoryOptions);

    if (!in_array($caseCategoryName, $caseCategoryOptions)) {
      return FALSE;
    }

    return TRUE;
  }

}
