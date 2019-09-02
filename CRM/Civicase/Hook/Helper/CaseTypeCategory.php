<?php

use CRM_Civicase_Helper_CaseCategory as CaseCategoryHelper;

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
    $caseCategoryName = strtolower($caseCategoryName);
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

  /**
   * Returns the case type ids for a case type category.
   *
   * @param string $caseCategoryName
   *   Case category name.
   *
   * @return array|null
   *   The case type id's e.g [1, 2, 3]
   */
  public static function getCaseTypesForCategory($caseCategoryName) {
    try {
      $result = civicrm_api3('CaseType', 'get', [
        'return' => ['id'],
        'case_type_category' => $caseCategoryName,
      ]);

      if ($result['count'] == 0) {
        return NULL;
      }

      return array_column($result['values'], 'id');
    } catch (Exception $e) {
    }

  }

  /**
   * Adds the case category word replacements array to Civi's locale.
   *
   * @param string $caseCategoryName
   *   Case category name.
   */
  public static function addWordReplacements($caseCategoryName) {
    CRM_Core_Resources::singleton()->flushStrings()->resetCacheCode();

    if (!$caseCategoryName) {
      return;
    }

    $wordReplacements = CaseCategoryHelper::getWordReplacements($caseCategoryName);
    if (empty($wordReplacements)) {
      return;
    }

    $locale = CRM_Core_I18n::getLocale();
    Civi::$statics[CRM_Core_I18n::class][$locale] = array_replace_recursive(
      Civi::$statics[CRM_Core_I18n::class][$locale],
      [
        'enabled' => [
          'wildcardMatch' => $wordReplacements,
        ],
      ]
    );
  }

}
