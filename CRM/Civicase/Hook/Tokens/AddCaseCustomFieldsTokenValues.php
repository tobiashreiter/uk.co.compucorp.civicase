<?php

use CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues as CaseTokenValuesHelper;

/**
 * Class for adding case custom fields token values.
 */
class CRM_Civicase_Hook_Tokens_AddCaseCustomFieldsTokenValues {

  /**
   * Case Id.
   *
   * @var int|null
   *   Case Id.
   */
  private $caseId;

  /**
   * Case Token Values helper.
   *
   * @var \CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues
   *   Case Custom field helper.
   */
  private $caseTokenValuesHelper;

  /**
   * Sets required class properties.
   *
   * @param \CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues $caseTokenValuesHelper
   *   Case token values helper.
   */
  public function __construct(CaseTokenValuesHelper $caseTokenValuesHelper) {
    $this->caseTokenValuesHelper = $caseTokenValuesHelper;
  }

  /**
   * Sets case custom field token values.
   *
   * @param array $values
   *   Token values.
   * @param array $cids
   *   Contact ids.
   * @param int $job
   *   Job id.
   * @param array $tokens
   *   Available tokens.
   * @param string $context
   *   Context name.
   */
  public function run(array &$values, array $cids, $job, array $tokens, $context) {
    $this->caseId = $this->caseTokenValuesHelper->getCaseId($values);
    if (!$this->shouldRun($tokens)) {
      return;
    }

    $this->setCaseCustomFieldTokenValues($values, $cids, $tokens);
  }

  /**
   * Sets case custom field values.
   *
   * Normally we would not do this but there is an issue of a mysql max table
   * join error on a site with a lot of case custom fields enabled.
   * To fix that this class CRM_Civicase_Event_Listener_CaseCustomFields was
   * created to prevent the loading of custom fields for cases without the
   * specific custom fields being passed. However, when replacing tokens for
   * sent emails, Civi expects the Case.get call to also return the case custom
   * fields with the other case parameters when no return value is specified.
   *
   * This function replaces the case custom fields token values as civi is
   * not able to do the values replacement because of the custom change we made
   * to avoid the 61 max table join error.
   *
   * @param array $values
   *   Token values.
   * @param array $cids
   *   Contact ids.
   * @param array $tokens
   *   Available tokens.
   */
  private function setCaseCustomFieldTokenValues(array &$values, array $cids, array $tokens) {
    $customValues = $this->caseTokenValuesHelper->getCustomFieldValues($this->caseId, $tokens['case_cf']);
    if (empty($customValues)) {
      return;
    }
    unset($customValues['id']);

    // We need to prepend the token category 'case_cf' to the custom field key
    // so it can be evaluated for the case_cf category when token is replaced.
    $customValuesNew = [];
    foreach ($customValues as $key => $customValue) {
      $customValuesNew['case_cf.' . $key] = $this->caseTokenValuesHelper->getTokenReplacementValue($key, $customValues);
    }

    foreach ($cids as $cid) {
      $values[$cid] = array_merge($values[$cid], $customValuesNew);
    }
  }

  /**
   * Decides whether the hook should run or not.
   *
   * @param array $tokens
   *   Available tokens.
   *
   * @return bool
   *   Whether this hook should run or not.
   */
  private function shouldRun(array $tokens) {
    return !empty($this->caseId) && !empty($tokens['case_cf']);
  }

}
