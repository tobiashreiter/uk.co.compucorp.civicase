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
      elseif (!empty($tokenList['text']) && $tokenList['text'] === self::CONTACT_TOKEN_TEXT) {
        $this->addClientTokens($tokenList['children'], $newTokenTree);
      }
      else {
        $this->addOtherTokens($tokenList['children'], $newTokenTree);
      }
    }
    CRM_Core_Resources::singleton()
      ->addScriptFile('uk.co.compucorp.civicase', 'js/token-tree.js')
      ->addSetting([
        'civicase-base' => [
          'custom_token_tree' => json_encode($newTokenTree),
        ],
      ]);
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
    foreach ($caseRoleTokens as $key => $token) {
      if ($token['id'] === 'case_roles.client') {
        continue;
      }
      $roleName = explode('-', $token['text']);
      $roleName = $roleName[0];
      $tokenName = 'Contact Role ' . $contactRoleCount . ' "' . trim($roleName) . '"';
      if (empty($newTokenTree[$tokenName])) {
        $contactRoleCount++;
        $tokenName = 'Contact Role ' . $contactRoleCount . ' "' . trim($roleName) . '"';
        $newTokenTree[$tokenName] = [
          'text' => $tokenName,
          'children' => [],
        ];
      }
      if (strpos($token['id'], '_custom_') !== FALSE) {
        if (empty($newTokenTree[$tokenName]['children'][1])) {
          $newTokenTree[$tokenName]['children'][1] = [
            'text' => 'Custom Fields',
            'children' => [$token],
          ];
        }
        else {
          $newTokenTree[$tokenName]['children'][1]['children'][] = $token;
        }
      }
      else {
        if (empty($newTokenTree[$tokenName]['children'][0])) {
          $newTokenTree[$tokenName]['children'][0] = [
            'text' => 'Core Fields',
            'children' => [$token],
          ];
        }
        else {
          $newTokenTree[$tokenName]['children'][0]['children'][] = $token;
        }
      }
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
      'text' => self::CASE_TOKEN_TEXT,
      'children' => [['text' => 'Core Fields', 'children' => $caseTokens]],
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
    $newTokenTree[self::CLIENT_TOKEN_TEXT] = [
      'text' => self::CLIENT_TOKEN_TEXT,
      'children' => [['text' => 'Core Fields', 'children' => $clientTokens]],
    ];
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
      'text' => self::CURRENT_USER_TOKEN_TEXT,
      'children' => [],
    ];
    foreach ($currentUserTokens as $key => $token) {
      if (strpos($token['id'], '_custom_') !== FALSE) {
        if (empty($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][1])) {
          $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][1] = [
            'text' => 'Custom Fields',
            'children' => [$token],
          ];
        }
        else {
          $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][1]['children'][] = $token;
        }
      }
      else {
        if (empty($newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0])) {
          $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0] = [
            'text' => 'Core Fields',
            'children' => [$token],
          ];
        }
        else {
          $newTokenTree[self::CURRENT_USER_TOKEN_TEXT]['children'][0]['children'][] = $token;
        }
      }
    }
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
        if (!empty($newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][1])) {
          $newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][1]['children'][] = $token;
        }
        else {
          $newTokenTree[self::CLIENT_TOKEN_TEXT]['children'][1] = [
            'text' => 'Custom Fields',
            'children' => [$token],
          ];
        }
      }
      elseif (strpos($token['id'], 'case.custom_') !== FALSE) {
        if (!empty($newTokenTree[self::CASE_TOKEN_TEXT]['children'][1])) {
          $newTokenTree[self::CASE_TOKEN_TEXT]['children'][1]['children'][] = $token;
        }
        else {
          $newTokenTree[self::CASE_TOKEN_TEXT]['children'][1] = [
            'text' => 'Custom Fields',
            'children' => [$token],
          ];
        }
      }
      else {
        if (!empty($newTokenTree[self::OTHER_TOKEN_TEXT])) {
          $newTokenTree[self::OTHER_TOKEN_TEXT]['children'][] = $token;
        }
        else {
          $newTokenTree[self::OTHER_TOKEN_TEXT] = [
            'text' => self::OTHER_TOKEN_TEXT,
            'children' => [$token],
          ];
        }

      }
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

}
