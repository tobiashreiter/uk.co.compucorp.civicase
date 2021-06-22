<?php

use CRM_Civicase_Hook_ValidateForm_SendBulkEmail as SendBulkEmailHook;

/**
 * Test class for the CRM_Civicase_Hook_ValidateForm_SendBulkEmail.
 *
 * @group headless
 */
class CRM_Civicase_Hook_SendBulkEmailTest extends BaseHeadlessTest {

  use CRM_Civicase_Helpers_SessionTrait;

  /**
   * Instance of the form sent on the hook.
   *
   * @var CRM_Core_Form
   */
  private $form;

  /**
   * Case Type created for using on tests.
   *
   * @var array
   */
  private $caseType;

  /**
   * {@inheritDoc}
   */
  public function setUp() {
    parent::setUp();

    $loggedInUser = CRM_Civicase_Test_Fabricator_Contact::fabricateWithEmail();
    $this->registerCurrentLoggedInContactInSession($loggedInUser['id']);

    $this->caseType = CRM_Civicase_Test_Fabricator_CaseType::fabricate()['id'];
  }

  /**
   * Test that the hook is executed when we use an email form.
   */
  public function testHookRunsWhenEmailFormIsUsed() {
    $contacts = [];
    $contacts[] = CRM_Civicase_Test_Fabricator_Contact::fabricateWithEmail()['id'];
    $cases = [];
    $cases[] = $this->createCaseForContact($contacts[0])['id'];

    $_REQUEST['caseRoles'] = $_GET['caseRoles'] = 'client';

    $this->form = new CRM_Contact_Form_Task_Email();
    $result = $this->runHook($cases, $contacts);

    $this->assertTrue($result);
  }

  /**
   * Test that the hook is not executed when we use another form.
   */
  public function testHookDoesNotRunWhenOtherFormIsUsed() {
    $contacts = [];
    $contacts[] = CRM_Civicase_Test_Fabricator_Contact::fabricateWithEmail()['id'];
    $cases = [];
    $cases[] = $this->createCaseForContact($contacts[0])['id'];

    $_REQUEST['caseRoles'] = $_GET['caseRoles'] = 'client';

    $this->form = new CRM_Contact_Form_Task();
    $result = $this->runHook($cases, $contacts);

    $this->assertFalse($result);
  }

  /**
   * Test that the hook is not executed when no cases are selected.
   */
  public function testHookDoesNotRunWhenNoCasesAreSelected() {
    $contacts = [];
    $cases = [];

    $_REQUEST['caseRoles'] = $_GET['caseRoles'] = 'client';

    $this->form = new CRM_Contact_Form_Task_Email();
    $result = $this->runHook($cases, $contacts);

    $this->assertFalse($result);
  }

  /**
   * Verify that one email activity is created for every case involved.
   */
  public function testHookCreateEmailActivitiesForCases() {
    $clientA = CRM_Civicase_Test_Fabricator_Contact::fabricateWithEmail()['id'];
    $clientB = CRM_Civicase_Test_Fabricator_Contact::fabricateWithEmail()['id'];
    $contact = CRM_Civicase_Test_Fabricator_Contact::fabricateWithEmail()['id'];
    $relationshipType = CRM_Civicase_Test_Fabricator_RelationshipType::fabricate()['id'];

    $caseA = $this->createCaseForContact($clientA)['id'];
    $caseB = $this->createCaseForContact($clientB)['id'];
    CRM_Civicase_Test_Fabricator_Relationship::fabricate([
      'case_id' => $caseA,
      'contact_id_a' => $clientA,
      'contact_id_b' => $contact,
      'relationship_type_id' => $relationshipType,
    ]);
    CRM_Civicase_Test_Fabricator_Relationship::fabricate([
      'case_id' => $caseB,
      'contact_id_a' => $clientB,
      'contact_id_b' => $contact,
      'relationship_type_id' => $relationshipType,
    ]);

    $_REQUEST['caseRoles'] = $_GET['caseRoles'] = "client,$relationshipType";

    $this->form = new CRM_Contact_Form_Task_Email();
    $this->runHook([$caseA, $caseB], [$clientA, $clientB, $contact]);

    $this->assertEquals(1, $this->countEmailActivitiesCreated($caseA));
    $this->assertEquals(1, $this->countEmailActivitiesCreated($caseB));
  }

  /**
   * Create a case for the given contact.
   *
   * @param int $contact
   *   ID of the contact.
   *
   * @return array
   *   Array with case information.
   */
  private function createCaseForContact(int $contact) {
    return CRM_Civicase_Test_Fabricator_Case::fabricate(
      [
        'contact_id' => $contact,
        'creator_id' => $contact,
        'case_type_id' => $this->caseType,
      ]
    );
  }

  /**
   * Perform the necessary setup for running the hook, and run it.
   *
   * @param array $cases
   *   Array with case ids.
   * @param array $contacts
   *   Array with contacts ids.
   *
   * @return bool
   *   Return the result of calling the hook.
   */
  private function runHook(array $cases, array $contacts) {
    $_REQUEST['allCaseIds'] = $_GET['allCaseIds'] = implode(',', $cases);

    $formName = 'Test Form';
    $fields = $files = $errors = [];
    $this->form->_contactIds = $contacts;
    $this->form->addElement('hidden', 'cc_id', 0);
    $this->form->addElement('hidden', 'bcc_id', 0);
    $this->form->addElement('hidden', 'subject', 'Test Subject');
    $this->form->addElement('hidden', 'text_message', 'Test Text Message');
    $this->form->addElement('hidden', 'html_message', 'Test Html Message');

    return (new SendBulkEmailHook())->run($formName, $fields, $files, $this->form, $errors);
  }

  /**
   * Count the email activities created for a given case.
   *
   * @param int $case
   *   Id of the case.
   *
   * @return int
   *   Number of activities that match the search parameters.
   */
  private function countEmailActivitiesCreated($case = NULL) {
    $activityTypeID = CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', 'Email');

    $activityParams = [
      'activity_type_id' => $activityTypeID,
      'status_id' => CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'status_id', 'Completed'),
    ];
    if (!empty($case)) {
      $activityParams['case_id'] = $case;
    }

    return civicrm_api3('Activity', 'getcount', $activityParams);
  }

}
