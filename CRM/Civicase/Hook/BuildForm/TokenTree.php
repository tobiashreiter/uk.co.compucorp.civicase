<?php

/**
 * Attaches a new token tree to the form.
 */
class CRM_Civicase_Hook_BuildForm_TokenTree {

  const CASE_ROLE_TOKEN_TEXT = 'Case Roles';

  const CURRENT_USER_TOKEN_TEXT = 'Current User';

  const CASE_TOKEN_TEXT = 'Case';

  const CONTACT_TOKEN_TEXT = 'Contact';

  const CLIENT_TOKEN_TEXT = 'Client';

  const OTHER_TOKEN_TEXT = 'Other';

  const ADDRESS_TOKEN_TEXT = 'Address';

  /**
   * All case and contact related custom fields.
   *
   * @var array
   */
  private $customFields = [];

  /**
   * Attaches a new token tree to the form.
   *
   * @param CRM_Core_Form $form
   *   Form object class.
   * @param string $formName
   *   Form name.
   */
  public function run(CRM_Core_Form &$form, $formName) {
    if (!$this->shouldRun($formName)) {
      return;
    }
    $this->fetchAllRelevantCustomFields();
    $this->attachNewTokenTreeToForm($form);
  }

  /**
   * Attaches a new token tree to the form.
   *
   * @param CRM_Core_Form $form
   *   Form object class.
   */
  private function attachNewTokenTreeToForm(CRM_Core_Form &$form) {
    $tokens = $form->get_template_vars('tokens');
    $newTokenTree = [];
    foreach ($tokens as $key => $tokenList) {
      if (!empty($tokenList['text']) && $tokenList['text'] === self::CASE_ROLE_TOKEN_TEXT) {
        $this->addCaseRoleTokens($tokenList['children'], $newTokenTree);
      }
      elseif (!empty($tokenList['text']) && $tokenList['text'] === self::CURRENT_USER_TOKEN_TEXT) {
        $this->addCurrentUserTokens($tokenList['children'], $newTokenTree);
      }
      elseif (!empty($tokenList['text']) && $tokenList['text'] === self::CASE_TOKEN_TEXT) {
        $this->addCaseTokens($tokenList['children'], $newTokenTree);
      }
      elseif (!empty($tokenList['text']) && in_array(
        $tokenList['text'],
        [self::CONTACT_TOKEN_TEXT, self::ADDRESS_TOKEN_TEXT]
        )) {
        $this->addClientTokens($tokenList['children'], $newTokenTree);
      }
      else {
        $this->addOtherTokens($tokenList['children'], $newTokenTree);
      }
    }
    $this->reFormatCustomTokens($newTokenTree);
    CRM_Core_Resources::singleton()
      ->addScriptFile('uk.co.compucorp.civicase', 'js/token-tree.js')
      ->addSetting([
        'civicase-base' => [
          'custom_token_tree' => json_encode(array_values($newTokenTree)),
        ],
      ]);
  }

  /**
   * Reformat the custom fields.
   *
   * @param array $newTokenTree
   *   Restructured token tree.
   */
  private function reFormatCustomTokens(array &$newTokenTree) {
    if (!empty($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0]['children'])) {
      $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0]['children']
        = array_values($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0]['children']);
    }
    if (!empty($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][1]['children'])) {
      $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][1]['children']
        = array_values($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][1]['children']);
    }
    if (!empty($newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][0]['children'])) {
      $newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][0]['children']
        = array_values($newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][0]['children']);
    }
    if (!empty($newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][1]['children'])) {
      $newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][1]['children']
        = array_values($newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][1]['children']);
    }
    if (!empty($newTokenTree[self::CASE_TOKEN_TEXT]['children'][0]['children'])) {
      $newTokenTree[self::CASE_TOKEN_TEXT]['children'][0]['children']
        = array_values($newTokenTree[self::CASE_TOKEN_TEXT]['children'][0]['children']);
    }
    if (!empty($newTokenTree[self::CASE_TOKEN_TEXT]['children'][1]['children'])) {
      $newTokenTree[self::CASE_TOKEN_TEXT]['children'][1]['children']
        = array_values($newTokenTree[self::CASE_TOKEN_TEXT]['children'][1]['children']);
    }
  }

  /**
   * Fetch all contact and case related custom fields.
   */
  private function fetchAllRelevantCustomFields() {
    $customFields = [];
    try {
      $customFields = civicrm_api3('CustomField', 'get', [
        'custom_group_id.extends' => [
          'IN' => ['Contact', 'Individual', 'Household', 'Organization', 'Case'],
        ],
        'options' => ['limit' => 0],
        'sequential' => 1,
        'return' => ['id', 'custom_group_id.title'],
      ]);
    }
    catch (Throwable $ex) {
    }
    if (!empty($customFields) && $customFields['is_error'] === 0) {
      foreach ($customFields['values'] as $field) {
        $this->customFields[$field['id']] = $field['custom_group_id.title'];
      }
    }
  }

  /**
   * Add case role tokens to the new token tree.
   *
   * @param array $caseRoleTokens
   *   Array of case role tokens.
   * @param array $newTokenTree
   *   Restructured token tree.
   */
  private function addCaseRoleTokens(array $caseRoleTokens, array &$newTokenTree) {
    $contactRoleCount = 0;
    $contactRoleTokens = [];
    foreach ($caseRoleTokens as $key => $token) {
      if ($token['id'] === '{case_roles.client}') {
        continue;
      }
      $roleName = explode('-', $token['text']);
      $roleName = $roleName[0];
      $tokenName = 'Contact Role ' . $contactRoleCount . ' "' . trim($roleName) . '"';
      if (empty($contactRoleTokens[$tokenName])) {
        $contactRoleCount++;
        $tokenName = 'Contact Role ' . $contactRoleCount . ' "' . trim($roleName) . '"';
        $contactRoleTokens[$tokenName] = [
          'id' => $this->clean($tokenName) . uniqid(),
          'text' => $tokenName,
          'children' => [],
        ];
      }
      if (strpos($token['id'], '_custom_') !== FALSE) {
        $this->addCustomTokens($contactRoleTokens, $tokenName, $token);
      }
      else {
        if (empty($contactRoleTokens[$tokenName]['children'][0])) {
          $contactRoleTokens[$tokenName]['children'][0] = [
            'id' => 'CoreFields' . uniqid(),
            'text' => 'Core Fields',
            'children' => [$token],
          ];
        }
        else {
          $contactRoleTokens[$tokenName]['children'][0]['children'][] = $token;
        }
      }
    }

    foreach ($contactRoleTokens as $key => $caseRoleToken) {
      $caseRoleToken['children'] = array_values($caseRoleToken['children']);
      if (!empty($caseRoleToken['children'][1]['children'])) {
        $caseRoleToken['children'][1]['children']
          = array_values($caseRoleToken['children'][1]['children']);
      }
      if (!empty($caseRoleToken['children'][0]['children'])) {
        $caseRoleToken['children'][0]['children']
          = array_values($caseRoleToken['children'][0]['children']);
      }
      $newTokenTree[$key] = $caseRoleToken;
    }
  }

  /**
   * Add case tokens to the new token tree.
   *
   * @param array $caseTokens
   *   Array of case tokens.
   * @param array $newTokenTree
   *   Restructured token tree.
   */
  private function addCaseTokens(array $caseTokens, array &$newTokenTree) {
    $newTokenTree[self::CASE_TOKEN_TEXT] = [
      'id' => self::CASE_TOKEN_TEXT,
      'text' => self::CASE_TOKEN_TEXT,
      'children' => [
        [
          'id' => 'CoreFields' . uniqid(),
          'text' => 'Core Fields',
          'children' => $caseTokens,
        ],
      ],
    ];
  }

  /**
   * Add client tokens to the new token tree.
   *
   * @param array $clientTokens
   *   Array of client tokens.
   * @param array $newTokenTree
   *   Restructured token tree.
   */
  private function addClientTokens(array $clientTokens, array &$newTokenTree) {
    if (empty($newTokenTree[self::CLIENT_TOKEN_TEXT])) {
      $newTokenTree[self::CLIENT_TOKEN_TEXT] = [
        'id' => self::CLIENT_TOKEN_TEXT,
        'text' => self::CLIENT_TOKEN_TEXT,
        'children' => [
          [
            'id' => 'CoreFields' . uniqid(),
            'text' => 'Core Fields',
            'children' => $clientTokens,
          ],
        ],
      ];
    }
    else {
      $newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][0]['children'] =
        array_merge($newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][0]['children'], $clientTokens);
    }
  }

  /**
   * Add current user tokens to the new token tree.
   *
   * @param array $currentUserTokens
   *   Array of current user tokens.
   * @param array $newTokenTree
   *   Restructured token tree.
   */
  private function addCurrentUserTokens(array $currentUserTokens, array &$newTokenTree) {
    $newTokenTree[self::CURRENT_USER_TOKEN_TEXT] = [
      'id' => $this->clean(self::CURRENT_USER_TOKEN_TEXT),
      'text' => self::CURRENT_USER_TOKEN_TEXT,
      'children' => [],
    ];
    foreach ($currentUserTokens as $key => $token) {
      if (strpos($token['id'], '_custom_') !== FALSE) {
        $this->addCustomTokens($newTokenTree, self::CURRENT_USER_TOKEN_TEXT, $token);
      }
      else {
        if (empty($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0])) {
          $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0] = [
            'id' => 'CoreFields' . uniqid(),
            'text' => 'Core Fields',
            'children' => [$token],
          ];
        }
        else {
          $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0]['children'][] = $token;
        }
      }
    }
    $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'] =
      array_values($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children']);
  }

  /**
   * Add all remaining tokens to the new token tree.
   *
   * @param array $otherTokens
   *   Array of tokens.
   * @param array $newTokenTree
   *   Restructured token tree.
   */
  private function addOtherTokens(array $otherTokens, array &$newTokenTree) {
    foreach ($otherTokens as $key => $token) {
      if (strpos($token['id'], 'contact.custom_') !== FALSE) {
        $this->addCustomTokens($newTokenTree, self::CLIENT_TOKEN_TEXT, $token);
      }
      elseif (strpos($token['id'], 'case.custom_') !== FALSE) {
        $this->addCustomTokens($newTokenTree, self::CASE_TOKEN_TEXT, $token);
      }
      else {
        if (!empty($newTokenTree[self::OTHER_TOKEN_TEXT])) {
          $newTokenTree[self::OTHER_TOKEN_TEXT]['children'][] = $token;
        }
        else {
          $newTokenTree[self::OTHER_TOKEN_TEXT] = [
            'id' => self::OTHER_TOKEN_TEXT,
            'text' => self::OTHER_TOKEN_TEXT,
            'children' => [$token],
          ];
        }

      }
    }
  }

  /**
   * Add custom tokens to a particular list.
   *
   * @param array $newTokenTree
   *   Restructured token tree.
   * @param string $label
   *   Label for the tokens.
   * @param array $token
   *   Token that is to be added.
   */
  private function addCustomTokens(array &$newTokenTree, $label, array $token) {
    $separatedId = explode('_', $token['id']);
    $customFieldId = $separatedId[1] ? rtrim($separatedId[count($separatedId) - 1], '}') : NULL;
    $customFieldLabel = $this->customFields[$customFieldId] ?? '';
    if (!empty($newTokenTree[$label]['children'][1])) {
      if (!empty($newTokenTree[$label]['children'][1]['children'][$customFieldLabel])) {
        $newTokenTree[$label]['children'][1]['children'][$customFieldLabel]['children'][] = $token;
      }
      else {
        $newTokenTree[$label]['children'][1]['children'][$customFieldLabel] = [
          'id' => $this->clean($customFieldLabel) . uniqid(),
          'text' => $customFieldLabel,
          'children' => [$token],
        ];
      }
    }
    else {
      $newTokenTree[$label]['children'][1] = [
        'id' => 'CustomFields' . uniqid(),
        'text' => 'Custom Fields',
        'children' => [
          $customFieldLabel =>
            [
              'id' => $this->clean($customFieldLabel) . uniqid(),
              'text' => $customFieldLabel,
              'children' => [$token],
            ],
        ],
      ];
    }
  }

  /**
   * Determines if the hook will run.
   *
   * This hook is only valid for the email and pdf case forms.
   *
   * @param string $formName
   *   Form name.
   *
   * @return bool
   *   Determines if the hook will run.
   */
  public function shouldRun($formName) {
    return CRM_Utils_Request::retrieve('caseid', 'Integer') &&
      in_array(
        $formName,
        [CRM_Contact_Form_Task_Email::class, CRM_Contact_Form_Task_PDF::class]
      );
  }

  /**
   * Removes special characters from a string.
   *
   * @param string $string
   *   String from which special characters should be removed.
   *
   * @return string
   *   Formatted string.
   */
  private function clean($string) {
    $string = str_replace(' ', '-', $string);

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
  }

}
