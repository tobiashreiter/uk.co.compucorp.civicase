<?php

use Civi\Token\Event\TokenValueEvent;

/**
 * Class for adding case custom fields token values.
 */
class CRM_Civicase_Hook_Tokens_AddCaseCustomFieldsTokenValues {

  /**
   * Evaluate custom fields and case role tokens.
   *
   * @param \Civi\Token\Event\TokenValueEvent $e
   *   TokenValue Event.
   */
  public static function evaluateCaseCustomFieldsTokens(TokenValueEvent $e) {
    $context = $e->getTokenProcessor()->context;
    $caseTokenValuesHelper = new CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues();
    $customTokens = $e->getTokenProcessor()->getMessageTokens()['case_cf'] ?? [];
    $caseRoleTokens = $e->getTokenProcessor()->getMessageTokens()['case_roles'] ?? [];
    $caseRoleValues = [];

    if (array_key_exists('schema', $context) && in_array('caseId', $context['schema'])) {
      foreach ($e->getRows() as $row) {
        if (!empty($row->context['caseId'])) {
          $caseId = $row->context['caseId'];
          $contactId = $row->context['contactId'];
          $customValues = $caseTokenValuesHelper->getCustomFieldValues($caseId, $customTokens);

          // Replace custom field tokens with their values.
          foreach ($customTokens as $token) {
            $value = $caseTokenValuesHelper->getTokenReplacementValue($token, $customValues);
            $row->format('text/plain')->tokens('case_cf', $token, $value);
            $row->format('text/html')->tokens('case_cf', $token, $value);
          }

          // If the token is being resolved through a webform-triggered
          // activity, the case role token extension might fail
          // to resolve the case role tokens
          // due to its inability to locate the case ID.
          // To address this, we manually reevaluate the token value here
          // by extracting the case ID from the token event.
          if (!self::isWebform()) {
            continue;
          }

          if (function_exists('casetokens_civicrm_tokenvalues') && !empty($caseRoleTokens)) {
            Civi::$statics['casetokens']['case_id'] = $row->context['caseId'];
            casetokens_civicrm_tokenvalues($caseRoleValues, [$contactId], NULL, ['case_roles' => $caseRoleTokens]);
          }

          // Replace case role tokens with their values.
          if (!empty($caseRoleValues)) {
            foreach ($caseRoleTokens as $token) {
              $row->format('text/plain')->tokens('case_roles', $token, $caseRoleValues[$contactId]['case_roles.' . $token] ?? '');
              $row->format('text/html')->tokens('case_roles', $token, $caseRoleValues[$contactId]['case_roles.' . $token] ?? '');
            }

          }
        }
      }

    }

  }

  /**
   * Detects the token activity is triggered by webform.
   */
  private static function isWebform() {
    return isset($_POST['form_id']) && stripos($_POST['form_id'], 'webform_client_form_') !== FALSE;
  }

}
