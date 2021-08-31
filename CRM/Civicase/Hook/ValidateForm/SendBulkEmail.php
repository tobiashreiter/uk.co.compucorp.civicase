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
   * Contains the original contacts to be notified, before this hook action.
   *
   * @var array|int[]
   */
  private $originalContactIds;

  /**
   * Contains the original emails to be notified, before this hook action.
   *
   * @var array|int[]
   */
  private $originalToContactEmails;

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

    $this->originalContactIds = $this->form->_contactIds;
    $this->originalToContactEmails = $this->form->_toContactEmails;

    $casesContactInfo = $this->getCasesContactInfo();
    $messageSentCount = 0;
    foreach ($casesContactInfo as $caseId => $contactIds) {
      $this->sendEmailForCase($caseId, $contactIds);

      $messageSentCount += count($contactIds);
    }

    if ($messageSentCount > count($this->originalContactIds)) {
      $this->updateStatusMessage($messageSentCount);
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

    $casesContactInfo = $this->getCaseClients($casesContactInfo);
    $casesContactInfo = $this->getCaseRoleContacts($casesContactInfo);

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
  private function getCaseClients(array $casesContactInfo) {
    if (!$this->isClientRoleSelected) {
      return $casesContactInfo;
    }

    $cases = civicrm_api3('Case', 'get', [
      'return' => ['contact_id'],
      'id' => ['IN' => $this->caseIds],
      'options' => ['limit' => 0],
    ])['values'];

    foreach ($cases as $caseId => $caseInfo) {
      foreach ($caseInfo['client_id'] as $clientId) {
        if (!in_array($clientId, $this->form->_contactIds)) {
          // The contact was not selected.
          continue;
        }
        $casesContactInfo[$caseId][] = $clientId;
      }
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
  private function getCaseRoleContacts(array $casesContactInfo) {
    if (count($this->caseRoles) === 0) {
      return $casesContactInfo;
    }

    $relationships = civicrm_api3('Relationship', 'get', [
      'sequential' => 1,
      'case_id' => ['IN' => $this->caseIds],
      'relationship_type_id' => ['IN' => $this->caseRoles],
      'is_active' => 1,
      'return' => ['case_id', 'contact_id_b'],
      'options' => ['limit' => 0],
    ])['values'];

    foreach ($relationships as $relationship) {
      if (!in_array($relationship['contact_id_b'], $this->form->_contactIds)) {
        // The contact was not selected.
        continue;
      }
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

    $toContactEmails = [];
    foreach ($contactIds as $contactId) {
      $originalPosition = array_search($contactId, $this->originalContactIds);
      $toContactEmails[] = $this->originalToContactEmails[$originalPosition];
    }
    $this->form->_toContactEmails = $toContactEmails;
    $this->form->submit($this->form->exportValues());
  }

  /**
   * Update the status message shown to the user.
   *
   * @param int $messageSentCount
   *   Number of messages sent.
   */
  private function updateStatusMessage(int $messageSentCount) {
    $status = CRM_Core_Session::singleton()->getStatus(TRUE)[0];

    $originalMessage = count($this->originalContactIds) === 1
      ? 'One message was sent successfully.'
      : count($this->originalContactIds) . ' messages were sent successfully.';

    $status['text'] = str_replace(
      $originalMessage,
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
