<?php

/**
 * Fabricates custom field.
 */
class CRM_Civicase_Test_Fabricator_CustomField {

  /**
   * Fabricates a custom field.
   *
   * @param array $params
   *   Parameters.
   *
   * @return array
   *   Results.
   */
  public static function fabricate(array $params = []) {
    $params = array_merge(static::getDefaultParams(), $params);

    if (empty($params['custom_group_id'])) {
      $params['custom_group_id'] = CRM_Civicase_Test_Fabricator_CustomGroup::fabricate()['id'];
    }

    $field = civicrm_api3('CustomField', 'create', $params);

    return array_shift($field['values']);
  }

  /**
   * Initialiazes Default parameters.
   *
   * @return array
   *   Default parameters.
   */
  public static function getDefaultParams() {
    return [
      'name' => md5(mt_rand()),
      'label' => md5(mt_rand()),
      'html_type' => 'Text',
    ];
  }

}
