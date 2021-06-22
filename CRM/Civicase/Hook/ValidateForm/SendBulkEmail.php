<?php

/**
 * Send bulk email.
 */
class CRM_Civicase_Hook_ValidateForm_SendBulkEmail {

  /**
   * Instance of the form sent on the hook.
   *
   * @var CRM_Core_Form
   */
  private $form;

  /**
   * Array with case roles to be notified.
   *
   * @var array|string[]
   */
  private $caseRoles;

  /**
   * Array of the case ids involved on the notification.
   *
   * @var array|string[]
   */
  private $caseIds;

  /**
   * Indicates if the client role is present on the roles to be notified.
   *
   * @var bool
   */
  private $isClientRoleSelected;

  /**
   * CRM_Civicase_Hook_ValidateForm_SendBulkEmail constructor.
   *
   * @throws CRM_Core_Exception
   */
  public function __construct() {
    $caseRoles = CRM_Utils_Request::retrieve('caseRoles', 'String');
    $this->caseRoles = $caseRoles ? explode(',', $caseRoles) : [];
    $caseIds = CRM_Utils_Request::retrieve('allCaseIds', 'String');
    $this->caseIds = $caseIds ? explode(',', $caseIds) : [];

    $clientRoleKey = array_search('client', $this->caseRoles, TRUE);
    $this->isClientRoleSelected = $clientRoleKey !== FALSE;
    if ($this->isClientRoleSelected) {
      unset($this->caseRoles[$clientRoleKey]);
    }
  }

  /**
   * Implement the send bulk functionality.
   *
   * @param string $formName
   *   Form Name.
   * @param array $fields
   *   Fields List.
   * @param array $files
   *   Files list.
   * @param CRM_Core_Form $form
   *   Form Class object.
   * @param array $errors
   *   Errors.
   *
   * @return bool
   *   TRUE if the hook ran, false otherwise.
   */
  public function run($formName, array &$fields, array &$files, CRM_Core_Form &$form, array &$errors) {
    $this->form = $form;
    if (!$this->shouldRun()) {
      return FALSE;
    }

    $uniqueContacts = count($this->form->_contactIds);

    $casesContactInfo = $this->getCasesContactInfo();
    $messageSentCount = 0;
    foreach ($casesContactInfo as $caseId => $contactIds) {
      $this->sendEmailForCase($caseId, $contactIds);

      $messageSentCount += count($contactIds);
    }

    if ($messageSentCount > $uniqueContacts) {
      $this->updateStatusMessage($uniqueContacts, $messageSentCount);
    }
    if (CRM_Utils_Array::value('snippet', $_GET) === 'json') {
      CRM_Core_Page_AJAX::returnJsonResponse([]);
    }

    return TRUE;
  }

  /**
   * Determines if the hook will run.
   *
   * @return bool
   *   Returns TRUE the Hook should run.
   */
  private function shouldRun() {
    return (
      get_class($this->form) === CRM_Contact_Form_Task_Email::class &&
      !empty($this->caseIds) &&
      (!empty($this->caseRoles) || $this->isClientRoleSelected)
    );
  }

  /**
   * Returns contact information associated with every case.
   *
   * Returns an array with case ids as keys as the respective contacts
   * as a value. The contacts are defined by the roles selected on the
   * interface.
   */
  private function getCasesContactInfo() {
    $casesContactInfo = [];

    $casesContactInfo = $this->parseClientContacts($casesContactInfo);
    $casesContactInfo = $this->parseNonClientContacts($casesContactInfo);

    return $casesContactInfo;
  }

  /**
   * Collect the contact information for case clients.
   *
   * @param array $casesContactInfo
   *   Current case contacts info, to be updated with client data.
   *
   * @return array
   *   Returns the received array with the new information.
   */
  private function parseClientContacts(array $casesContactInfo) {
    if (!$this->isClientRoleSelected) {
      return $casesContactInfo;
    }

    $cases = civicrm_api3('Case', 'get', [
      'return' => ['contact_id'],
      'id' => ['IN' => $this->caseIds],
    ])['values'];

    foreach ($cases as $caseId => $caseInfo) {
      $casesContactInfo[$caseId] = $caseInfo['client_id'];
    }

    return $casesContactInfo;
  }

  /**
   * Collect the contact information for case roles different to client.
   *
   * @param array $casesContactInfo
   *   Current case contacts info, to be updated with other roles.
   *
   * @return array
   *   Returns the received array with the new information.
   */
  private function parseNonClientContacts(array $casesContactInfo) {
    if (count($this->caseRoles) === 0) {
      return $casesContactInfo;
    }

    $relationships = civicrm_api3('Relationship', 'get', [
      'sequential' => 1,
      'case_id' => ['IN' => $this->caseIds],
      'relationship_type_id' => ['IN' => $this->caseRoles],
      'is_active' => 1,
      'return' => ['case_id', 'contact_id_b'],
    ])['values'];

    foreach ($relationships as $relationship) {
      $casesContactInfo[$relationship['case_id']] = $casesContactInfo[$relationship['case_id']] ?? [];
      $casesContactInfo[$relationship['case_id']][] = $relationship['contact_id_b'];
    }

    return $casesContactInfo;
  }

  /**
   * Send the emails for the contacts associated with the given case.
   *
   * @param int $caseId
   *   The Id of the Case.
   * @param array|int[] $contactIds
   *   Array with contact Ids.
   */
  private function sendEmailForCase(int $caseId, array $contactIds) {
    $_GET['caseid'] = $_REQUEST['caseid'] = $caseId;
    $this->form->_caseId = $caseId;
    $this->form->_contactIds = $contactIds;
    $this->form->submit($this->form->exportValues());
  }

  /**
   * Update the status message shown to the user.
   *
   * @param int $uniqueContacts
   *   Number of unique contacts to be notified.
   * @param int $messageSentCount
   *   Number of messages sent.
   */
  private function updateStatusMessage(int $uniqueContacts, int $messageSentCount) {
    $status = CRM_Core_Session::singleton()->getStatus(TRUE)[0];
    $status['text'] = str_replace(
      "$uniqueContacts messages were sent successfully.",
      "$messageSentCount messages were sent successfully.",
      $status['text']
    );
    CRM_Core_Session::setStatus(
      $status['text'],
      $status['title'] ?? '',
      $status['type'] ?? '',
      $status['options'] ?? []
    );
  }

}
