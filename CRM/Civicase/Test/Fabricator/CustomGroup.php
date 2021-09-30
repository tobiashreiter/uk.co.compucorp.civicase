<?php

/**
 * Fabricates custom group.
 */
class CRM_Civicase_Test_Fabricator_CustomGroup {

  /**
   * Fabricates a custom group.
   *
   * @param array $params
   *   Parameters.
   *
   * @return array
   *   Results.
   */
  public static function fabricate(array $params = []) {
    $params = array_merge(static::getDefaultParams(), $params);
    $group = civicrm_api3('CustomGroup', 'create', [
      'title' => $params['title'],
      'extends' => $params['extends'],
    ]);

    return array_shift($group['values']);
  }

  /**
   * Initialiazes Default parameters.
   *
   * @return array
   *   Default parameters.
   */
  public static function getDefaultParams() {
    return ['title' => md5(mt_rand()), 'extends' => ['0' => 'Case']];
  }

}
