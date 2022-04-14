<?php

use CRM_Civicase_APIHelpers_GenericApi as GenericApiHelper;

/**
 * ActivityQueryApi API Helper Class.
 */
class CRM_Civicase_APIHelpers_ActivityQueryApi {

  /**
   * Validates the Case Query related API parameters.
   *
   * @param array $params
   *   API parameters.
   */
  public function validateParameters(array $params) {
    if (!empty($params['params']) && !empty($params['id'])) {
      throw new API_Exception("Please send either the params or Id");
    }

    if (empty($params['params']) && empty($params['id'])) {
      throw new API_Exception("Both params and Id cannot be empty");
    }
  }

  /**
   * Returns the parameters for making calls to Activity.get.
   *
   * @param array $params
   *   API parameters.
   *
   * @return array|string
   *   The parameters for making call to Activity.get.
   */
  public function getActivityGetRequestApiParams(array $params) {
    $genericApiHelper = new GenericApiHelper();
    $apiParams = '';

    if (!empty($params['id'])) {
      $apiParams = ['id' => ['IN' => $genericApiHelper->getParameterValue($params, 'id')]];
    }

    if (!empty($params['params'])) {
      $apiParams = $params['params'];
    }

    return $apiParams;
  }

  /**
   * Copy tags from one activity to another activity.
   *
   * @param int $activityId
   *   ID of the activity that is being copied.
   * @param int $newActivityId
   *   ID of the new activity.
   * @param string $mode
   *   Mode can either be move or copy.
   */
  public function transferActivityTags($activityId, $newActivityId, $mode = 'copy') {
    $result = civicrm_api3('EntityTag', 'get', [
      'sequential' => 1,
      'entity_table' => "civicrm_activity",
      'entity_id' => $activityId,
    ]);

    array_walk($result['values'], function ($tag) use ($newActivityId, $mode) {
      $tag['entity_id'] = $newActivityId;
      if ($mode === 'copy') {
        unset($tag['id']);
      }

      civicrm_api3('EntityTag', 'create', $tag);
    });
  }

}
