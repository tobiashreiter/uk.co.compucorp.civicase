<?php

use CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues as CaseTokenValuesHelper;

/**
 * CaseTokenValues Test class.
 *
 * @group headless
 */
class CRM_Civicase_Hook_Tokens_Helper_CaseTokenValuesTest extends BaseHeadlessTest {

  /**
   * Test case id can be gotten from URL.
   */
  public function testGetCaseIdReturnsCorrectlyWhenPresentInUrl() {
    $caseId = 1;
    $_REQUEST['caseid'] = $caseId;
    $caseTokenValuesHelper = new CaseTokenValuesHelper();
    $this->assertEquals($caseId, $caseTokenValuesHelper->getCaseId([]));
  }

  /**
   * Test case id can be gotten from entry URL.
   */
  public function testGetCaseIdReturnsCorrectlyWhenPresentInEntryUrl() {
    $caseId = 1;
    $_REQUEST['entryUrl'] = 'civicrm/test?caseid=' . $caseId;
    $caseTokenValuesHelper = new CaseTokenValuesHelper();
    $this->assertEquals($caseId, $caseTokenValuesHelper->getCaseId([]));
  }

  /**
   * Test case Id can be gotten from token values.
   */
  public function testGetCaseIdReturnsCorrectlyWhenPresentInToken() {
    $caseId = 1;
    $tokenValues[] = ['case.id' => $caseId];
    $caseTokenValuesHelper = new CaseTokenValuesHelper();
    $this->assertEquals($caseId, $caseTokenValuesHelper->getCaseId($tokenValues));
  }

}
