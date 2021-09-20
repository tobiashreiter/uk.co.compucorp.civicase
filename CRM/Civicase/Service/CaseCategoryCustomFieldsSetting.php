<?php

/**
 * Case Category Custom Fields Setting class.
 */
class CRM_Civicase_Service_CaseCategoryCustomFieldsSetting {
  const SETTING_NAME = 'case_category_custom_fields';

  /**
   * Deletes the custom field values for the given case category ID.
   *
   * @param string $caseCategoryId
   *   Case Category ID value.
   */
  public function delete($caseCategoryId) {
    $allCustomFields = $this->getAll();

    if (empty($allCustomFields) || !isset($allCustomFields[$caseCategoryId])) {
      return;
    }

    unset($allCustomFields[$caseCategoryId]);
    Civi::settings()->set(
      self::SETTING_NAME,
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
  public function get($caseCategoryId) {
    $allCustomFields = $this->getAll();

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
  public function save($caseCategoryId, array $customFields) {
    $allCustomFields = $this->getAll();
    $allCustomFields[$caseCategoryId] = $customFields;

    Civi::settings()->set(self::SETTING_NAME, $allCustomFields);
  }

  /**
   * Returns the custom field values for all case categories.
   *
   * @return array
   *   All Case Category custom field values.
   */
  private function getAll() {
    $allCustomFields = Civi::settings()->get(self::SETTING_NAME);

    return !empty($allCustomFields)
      ? $allCustomFields
      : [];
  }

}
