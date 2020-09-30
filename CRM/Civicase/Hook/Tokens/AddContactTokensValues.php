<?php

/**
 * Add current user token values.
 */
class CRM_Civicase_Hook_Tokens_AddContactTokensValues {

  /**
   * Service for fetching contact fields.
   *
   * @var CRM_Civicase_Service_ContactFieldsProvider
   */
  private $contactFieldsService;

  /**
   * Service for fetching contact custom fields.
   *
   * @var CRM_Civicase_Service_ContactCustomFieldsProvider
   */
  private $contactCustomFieldsService;

  /**
   * CRM_Civicase_Hook_Tokens_AddContactTokens constructor.
   *
   * @param CRM_Civicase_Service_ContactFieldsProvider $contactFieldsService
   *   Service for fetching contact fields.
   * @param CRM_Civicase_Service_ContactCustomFieldsProvider $contactCustomFieldsService
   *   Service for fetching contact custom fields.
   */
  public function __construct(
    CRM_Civicase_Service_ContactFieldsProvider $contactFieldsService,
    CRM_Civicase_Service_ContactCustomFieldsProvider $contactCustomFieldsService) {
    $this->contactFieldsService = $contactFieldsService;
    $this->contactCustomFieldsService = $contactCustomFieldsService;
  }

  /**
   * Add current user token values.
   *
   * @param array $values
   *   Token values.
   * @param array $cids
   *   Contact ids.
   * @param int $job
   *   Job id.
   * @param array $tokens
   *   Token names that are used actually.
   * @param string $context
   *   Context name.
   */
  public function run(array &$values, array $cids, $job, array $tokens, $context) {
    if (!$this->shouldRun($tokens)) {
      return;
    }
    $contactFields = $this->contactFieldsService->get();
    $customFields = $this->contactCustomFieldsService->get();
    try {
      $contactId = CRM_Core_Session::singleton()->getLoggedInContactID();
      $contactValues = civicrm_api3('contact', 'getsingle', array(
        'id' => $contactId,
        'return' => array_merge($contactFields, array_keys($customFields)),
      ));
      $currentUsersContact = [];
      foreach ($contactValues as $k => $value) {
        if (strpos($k, 'civicrm_value_') !== FALSE) {
          continue;
        }
        $k = (strpos($k, 'custom_') !== FALSE) ? $customFields[$k] : $k;
        if (in_array($k, array_merge($contactFields, $customFields))) {
          $key = 'current_user.contact_' . $k;
          $currentUsersContact[$key] = $value;
        }
      }

      foreach ($cids as $cid) {
        $values[$cid] = empty($values[$cid]) ? $currentUsersContact : array_merge($values[$cid], $currentUsersContact);
      }
    }
    catch (Throwable $ex) {
    }
  }

  /**
   * Decides whether the hook should run or not.
   *
   * @param array $tokens
   *   List of tokens that are used.
   *
   * @return bool
   *   Whether this hook should run or not.
   */
  private function shouldRun(array $tokens) {
    return !empty($tokens[CRM_Civicase_Hook_Tokens_AddContactTokens::TOKEN_KEY]);
  }

}
