<?php

use CRM_Civicase_Hook_BuildForm_TokenTree as TokenTree;

/**
 * Test class for the CRM_Civicase_Hook_BuildForm_TokenTree.
 *
 * @group headless
 */
class CRM_Civicase_Hook_BuildForm_TokenTreeTest extends BaseHeadlessTest {

  /**
   * Contact related custom field.
   *
   * @var array
   */
  private $contactCustomField = [];

  /**
   * Case related custom field.
   *
   * @var array
   */
  private $caseCustomField = [];

  /**
   * Test the run method.
   */
  public function testRun() {
    $this->setContactCustomFields();
    $this->setCaseCustomFields();
    $form = new CRM_Case_Form_Task_Email();
    $form->assign('tokens', $this->getTokens());
    $_GET['caseid'] = $_REQUEST['caseid'] = 1;
    $hook = new TokenTree();
    $hook->run($form, CRM_Case_Form_Task_Email::class);
    $setting = CRM_Core_Resources::singleton()->getSettings();
    $this->assertNotEmpty($setting['civicase-base']['custom_token_tree']);
    $newTokenTree = $this->format(json_decode($setting['civicase-base']['custom_token_tree'], TRUE));
    $this->verifyCurrentUserTokens($newTokenTree);
    $this->verifyCaseRoleTokens($newTokenTree);
    $this->verifyClientTokens($newTokenTree);
    $this->verifyCaseTokens($newTokenTree);
  }

  /**
   * Provides tokens for run method testing.
   *
   * @return array
   *   List of tokens.
   */
  private function getTokens() {
    return [
      [
        'text' => 'Case Roles',
        'children' => [
          [
            'id' => '{case_roles.benefits_specialist_id}',
            'text' => 'Benefits Specialist - Contact ID',
          ],
          [
            'id' => '{case_roles.benefits_specialist_custom_' . $this->contactCustomField[0]['id'] . '}',
            'text' => 'Benefits Specialist - ' . $this->contactCustomField[0]['name'],
          ],
          [
            'id' => '{case_roles.health_services_coordinator_contact_sub_type}',
            'text' => 'Health Services Coordinator - Contact Subtype',
          ],
          [
            'id' => '{case_roles.health_services_coordinator_custom_' . $this->contactCustomField[0]['id'] . '}',
            'text' => 'Health Services Coordinator - ' . $this->contactCustomField[0]['name'],
          ],
        ],
      ],
      [
        'text' => 'Current User',
        'children' => [
          [
            'id' => '{current_user.contact_city}',
            'text' => 'Current User City',
          ],
          [
            'id' => '{current_user.contact_custom_' . $this->contactCustomField[0]['id'] . '}',
            'text' => 'Current User ' . $this->contactCustomField[0]['name'],
          ],
        ],
      ],
      [
        'text' => $this->caseCustomField[0]['name'],
        'children' => [
          [
            'id' => '{case.custom_' . $this->caseCustomField[0]['id'] . '}',
            'text' => $this->caseCustomField[0]['name'],
          ],
        ],
      ],
      [
        'text' => 'Case',
        'children' => [
          [
            'id' => '{case.id}',
            'text' => 'Case Id',
          ],
        ],
      ],
      [
        'text' => $this->caseCustomField[1]['name'],
        'children' => [
          [
            'id' => '{case.custom_' . $this->caseCustomField[1]['id'] . '}',
            'text' => $this->caseCustomField[1]['name'],
          ],
        ],
      ],
      [
        'text' => $this->contactCustomField[0]['name'],
        'children' => [
          [
            'id' => '{contact.custom_' . $this->contactCustomField[0]['id'] . '}',
            'text' => $this->contactCustomField[0]['name'],
          ],
        ],
      ],
      [
        'text' => 'Contact',
        'children' => [
          [
            'id' => '{contact.addressee_id}',
            'text' => 'Addressee ID',
          ],
        ],
      ],
      [
        'text' => $this->contactCustomField[1]['name'],
        'children' => [
          [
            'id' => '{contact.custom_' . $this->contactCustomField[1]['id'] . '}',
            'text' => $this->contactCustomField[1]['name'],
          ],
        ],
      ],
    ];
  }

  /**
   * Format new token tree.
   *
   * @param array $tokens
   *   List of tokens.
   *
   * @return array
   *   Formatted tokens.
   */
  private function format(array $tokens) {
    $formattedTokens = [];
    foreach ($tokens as $token) {
      $formattedTokens[$token['text']] = $token;
    }

    return $formattedTokens;
  }

  /**
   * Verify current user tokens.
   *
   * @param array $newTokenTree
   *   List of tokens.
   */
  private function verifyCurrentUserTokens(array $newTokenTree) {
    $this->assertNotEmpty($newTokenTree[TokenTree::CURRENT_USER_TOKEN_TEXT]);
    $this->assertEquals(
      '{current_user.contact_city}',
      $newTokenTree[TokenTree::CURRENT_USER_TOKEN_TEXT]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Current User City',
      $newTokenTree[TokenTree::CURRENT_USER_TOKEN_TEXT]['children'][0]['children'][0]['text']
    );
    $this->assertEquals(
      '{current_user.contact_custom_' . $this->contactCustomField[0]['id'] . '}',
      $newTokenTree[TokenTree::CURRENT_USER_TOKEN_TEXT]['children'][1]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Current User ' . $this->contactCustomField[0]['name'],
      $newTokenTree[TokenTree::CURRENT_USER_TOKEN_TEXT]['children'][1]['children'][0]['children'][0]['text']
    );
  }

  /**
   * Verify case role tokens.
   *
   * @param array $newTokenTree
   *   List of tokens.
   */
  private function verifyCaseRoleTokens(array $newTokenTree) {
    $this->assertNotEmpty($newTokenTree['Benefits Specialist']);
    $this->assertEquals(
      '{case_roles.benefits_specialist_id}',
      $newTokenTree['Benefits Specialist']['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Benefits Specialist - Contact ID',
      $newTokenTree['Benefits Specialist']['children'][0]['children'][0]['text']
    );
    $this->assertEquals(
      '{case_roles.benefits_specialist_custom_' . $this->contactCustomField[0]['id'] . '}',
      $newTokenTree['Benefits Specialist']['children'][1]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Benefits Specialist - ' . $this->contactCustomField[0]['name'],
      $newTokenTree['Benefits Specialist']['children'][1]['children'][0]['children'][0]['text']
    );
    $this->assertNotEmpty($newTokenTree['Health Services Coordinator']);
    $this->assertEquals(
      '{case_roles.health_services_coordinator_contact_sub_type}',
      $newTokenTree['Health Services Coordinator']['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Health Services Coordinator - Contact Subtype',
      $newTokenTree['Health Services Coordinator']['children'][0]['children'][0]['text']
    );
    $this->assertEquals(
      '{case_roles.health_services_coordinator_custom_' . $this->contactCustomField[0]['id'] . '}',
      $newTokenTree['Health Services Coordinator']['children'][1]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Health Services Coordinator - ' . $this->contactCustomField[0]['name'],
      $newTokenTree['Health Services Coordinator']['children'][1]['children'][0]['children'][0]['text']
    );
  }

  /**
   * Verify client tokens.
   *
   * @param array $newTokenTree
   *   List of tokens.
   */
  private function verifyClientTokens(array $newTokenTree) {
    $this->assertNotEmpty($newTokenTree[TokenTree::RECIPIENT_TOKEN_TEXT]);
    $this->assertEquals(
      '{contact.addressee_id}',
      $newTokenTree[TokenTree::RECIPIENT_TOKEN_TEXT]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Addressee ID',
      $newTokenTree[TokenTree::RECIPIENT_TOKEN_TEXT]['children'][0]['children'][0]['text']
    );
    $this->assertEquals(
      '{contact.custom_' . $this->contactCustomField[0]['id'] . '}',
      $newTokenTree[TokenTree::RECIPIENT_TOKEN_TEXT]['children'][1]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      $this->contactCustomField[0]['name'],
      $newTokenTree[TokenTree::RECIPIENT_TOKEN_TEXT]['children'][1]['children'][0]['children'][0]['text']
    );
    $this->assertEquals(
      '{contact.custom_' . $this->contactCustomField[1]['id'] . '}',
      $newTokenTree[TokenTree::RECIPIENT_TOKEN_TEXT]['children'][1]['children'][0]['children'][1]['id']
    );
    $this->assertEquals(
      $this->contactCustomField[1]['name'],
      $newTokenTree[TokenTree::RECIPIENT_TOKEN_TEXT]['children'][1]['children'][0]['children'][1]['text']
    );
  }

  /**
   * Verify case tokens.
   *
   * @param array $newTokenTree
   *   List of tokens.
   */
  private function verifyCaseTokens(array $newTokenTree) {
    $this->assertNotEmpty($newTokenTree[TokenTree::CASE_TOKEN_TEXT]);
    $this->assertEquals(
      '{case.id}',
      $newTokenTree[TokenTree::CASE_TOKEN_TEXT]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      'Case Id',
      $newTokenTree[TokenTree::CASE_TOKEN_TEXT]['children'][0]['children'][0]['text']
    );
    $this->assertEquals(
      '{case_cf.custom_' . $this->caseCustomField[0]['id'] . '}',
      $newTokenTree[TokenTree::CASE_TOKEN_TEXT]['children'][1]['children'][0]['children'][0]['id']
    );
    $this->assertEquals(
      $this->caseCustomField[0]['name'],
      $newTokenTree[TokenTree::CASE_TOKEN_TEXT]['children'][1]['children'][0]['children'][0]['text']
    );
    $this->assertEquals(
      '{case_cf.custom_' . $this->caseCustomField[1]['id'] . '}',
      $newTokenTree[TokenTree::CASE_TOKEN_TEXT]['children'][1]['children'][0]['children'][1]['id']
    );
    $this->assertEquals(
      $this->caseCustomField[1]['name'],
      $newTokenTree[TokenTree::CASE_TOKEN_TEXT]['children'][1]['children'][0]['children'][1]['text']
    );
  }

  /**
   * Set contact related custom field.
   */
  private function setContactCustomFields() {
    $contactGroupParams = [
      'extends' => 'Contact',
      'name' => 'test-contact' . uniqid(),
      'title' => 'Test Contact' . uniqid(),
    ];
    $contactCustomGroup = civicrm_api3(
      'CustomGroup',
      'create',
      $contactGroupParams
    );
    if (empty($contactCustomGroup['id'])) {
      return;
    }
    $contactCustomFields = [];
    $contactCustomFields[] = $this->createCustomField($contactCustomGroup['id']);
    $contactCustomFields[] = $this->createCustomField($contactCustomGroup['id']);
    $i = 0;
    foreach ($contactCustomFields as $contactCustomField) {
      $this->contactCustomField[$i]['id'] = $contactCustomField['id'];
      $this->contactCustomField[$i]['name'] = $contactCustomField['name'];
      $i++;
    }
  }

  /**
   * Set case related custom field.
   */
  private function setCaseCustomFields() {
    $caseGroupParams = [
      'extends' => 'Case',
      'name' => 'test-case' . uniqid(),
      'title' => 'Test Case' . uniqid(),
    ];
    $caseCustomGroup = civicrm_api3(
      'CustomGroup',
      'create',
      $caseGroupParams
    );
    if (empty($caseCustomGroup['id'])) {
      return;
    }

    $caseCustomFields = [];
    $caseCustomFields[] = $this->createCustomField($caseCustomGroup['id']);
    $caseCustomFields[] = $this->createCustomField($caseCustomGroup['id']);
    $i = 0;
    foreach ($caseCustomFields as $caseCustomField) {
      $this->caseCustomField[$i]['id'] = $caseCustomField['id'];
      $this->caseCustomField[$i]['name'] = $caseCustomField['name'];
      $i++;
    }
  }

  /**
   * Create custom field.
   *
   * @param int $customGroupId
   *   Custom group id.
   *
   * @return array
   *   Result of custom field creation.
   */
  private function createCustomField($customGroupId) {
    $result = [];
    $default = [
      'custom_group_id' => $customGroupId,
      'name' => 'test' . uniqid(),
      'label' => 'Test' . uniqid(),
      'data_type' => 'Boolean',
      'default_value' => 1,
      'html_type' => 'Radio',
      'required' => 1,
      'sequential' => 1,
    ];
    try {
      $result = civicrm_api3(
        'CustomField',
        'create',
        $default
      );
      $result = $result['values'][0];
    }
    catch (Throwable  $ex) {
    }

    return $result;
  }

}
