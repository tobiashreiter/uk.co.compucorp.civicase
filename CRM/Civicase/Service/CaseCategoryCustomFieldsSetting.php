<?php

/**
 * Case Category Custom Fields Setting class.
 */
class CRM_Civicase_Service_CaseCategoryCustomFieldsSetting {
  const CASE_CATEGORY_CUSTOM_FIELDS_SETTING_NAME = 'case_category_custom_fields';

  /**
   * Deletes the custom field values for the given case category ID.
   *
   * @param string $caseCategoryId
   *   Case Category ID value.
   */
  public static function delete($caseCategoryId) {
    $allCustomFields = self::getAll();

    if (empty($allCustomFields) || !isset($allCustomFields[$caseCategoryId])) {
      return;
    }

    unset($allCustomFields[$caseCategoryId]);
    Civi::settings()->set(
      self::CASE_CATEGORY_CUSTOM_FIELDS_SETTING_NAME,
      $allCustomFields
    );
  }

  /**
   * Returns the custom field values for the given case category ID.
   *
   * @param string $caseCategoryId
   *   Case Category ID value.
   *
   * @return array
   *   Case Category custom field values.
   */
  public static function get($caseCategoryId) {
    $allCustomFields = self::getAll();

    return !empty($allCustomFields[$caseCategoryId])
      ? $allCustomFields[$caseCategoryId]
      : NULL;
  }

  /**
   * Saves the custom field values for the given case category ID.
   *
   * @param string $caseCategoryId
   *   Case Category ID value.
   * @param array $customFields
   *   Case Category custom field values.
   */
  public static function save($caseCategoryId, array $customFields) {
    $allCustomFields = self::getAll();
    $allCustomFields[$caseCategoryId] = $customFields;

    Civi::settings()->set(self::CASE_CATEGORY_CUSTOM_FIELDS_SETTING_NAME, $allCustomFields);
  }

  /**
   * Returns the custom field values for all case categories.
   *
   * @return array
   *   All Case Category custom field values.
   */
  private static function getAll() {
    $allCustomFields = Civi::settings()->get(self::CASE_CATEGORY_CUSTOM_FIELDS_SETTING_NAME);

    return !empty($allCustomFields)
      ? $allCustomFields
      : [];
  }

}
