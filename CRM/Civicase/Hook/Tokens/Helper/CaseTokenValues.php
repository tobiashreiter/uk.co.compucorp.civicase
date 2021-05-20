<?php

/**
 * CaseCustomFieldValues helper class.
 */
class CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues {

  /**
   * Get case custom field values.
   *
   * @param int $caseId
   *   Case ID.
   * @param array $customFieldNames
   *   Custom field names.
   *
   * @return array
   *   Case custom field values
   */
  public function getCustomFieldValues($caseId, array $customFieldNames) {
    return civicrm_api3('Case', 'getsingle', [
      'return' => $customFieldNames,
      'id' => $caseId,
    ]);
  }

  /**
   * Returns the case Id.
   *
   * Returns NULL if no case id is found. Means it is not a case related
   * form or page in that instance.
   *
   * @param array $contactTokenValues
   *   Contact token values.
   *
   * @return int|null
   *   Case Id.
   */
  public function getCaseId(array $contactTokenValues) {
    $caseId = CRM_Utils_Request::retrieve('caseid', 'Positive');
    if (!empty($caseId)) {
      return $caseId;
    }

    if (!empty($_POST['entryURL'])) {
      $urlParams = parse_url(htmlspecialchars_decode($_POST['entryURL']), PHP_URL_QUERY);
      parse_str($urlParams, $urlParams);

      if (!empty($urlParams['caseid'])) {
        return $urlParams['caseid'];
      }
    }

    if (!empty($contactTokenValues)) {
      $caseContactData = current($contactTokenValues);
      if (!empty($caseContactData['case.id'])) {
        return $caseContactData['case.id'];
      }
    }

    return NULL;
  }

}
