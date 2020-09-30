<?php

/**
 * Provides contacts custom fields.
 */
class CRM_Civicase_Service_ContactCustomFieldsProvider {

  /**
   * Provides contacts custom fields.
   *
   * @return array
   *   List of custom fields that extends contacts.
   */
  public function get() {
    $fields = [];
    try {
      $customFields = civicrm_api3('CustomField', 'get', [
        'custom_group_id.extends' => [
          'IN' => ['Contact', 'Individual', 'Household', 'Organization'],
        ],
      ]);
      if ($customFields['values']) {
        foreach ($customFields['values'] as $k => $item) {
          $fields['custom_' . $k] = $item['name'];
        }
      }
    }
    catch (Throwable $ex) {
    }

    return $fields;
  }

}
