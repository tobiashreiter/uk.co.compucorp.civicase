<?php

use Civi\API\Api3SelectQuery;

/**
 * Extended API 3 Select Query Class.
 *
 * It allows replacing protected fields such as the select fields.
 */
class CRM_Civicase_APIHelpers_ExtendedApi3SelectQuery extends Api3SelectQuery {

  /**
   * Replaces the stored select fields with new ones.
   *
   * @param array $fields
   *   The new select fields.
   */
  public function setSelectFields(array $fields) {
    $this->selectFields = $fields;
  }

}
