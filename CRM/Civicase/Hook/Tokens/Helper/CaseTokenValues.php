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
   * Returns the value for the custom field token.
   *
   * We are using the civicrm replacement function here because
   * it takes care of complex replacement such as values for checkboxes,
   * multi-selects and radio fields.
   *
   * @param string $token
   *   Token to get replacement value for.
   * @param array $customFieldValues
   *   Array of custom field keys and values.
   *
   * @return string
   *   Token replacement value.
   */
  public function getTokenReplacementValue($token, array $customFieldValues) {
    return CRM_Utils_Token::getApiTokenReplacement('case', $token, $customFieldValues);
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
