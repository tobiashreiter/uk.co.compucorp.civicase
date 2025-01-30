<?php

use CRM_Civicase_Hook_Tokens_Helper_CaseTokenValues as CaseTokenValuesHelper;
use CRM_Civicase_Hook_Tokens_AddCaseCustomFieldsTokenValues as AddCaseCustomFieldsTokenValueHook;

/**
 * Add CaseCustomFieldsTokenValues Test class.
 *
 * @group headless
 */
class CRM_Civicase_Hook_Tokens_AddCaseCustomFieldsTokenValuesTest extends BaseHeadlessTest {

  /**
   * Test case custom field token values are added.
   *
   * @group headless
   */
  public function testCustomFieldTokenValuesAreAddedWhenCaseIdIsPresent() {
    $caseCustomFieldValues = [
      'custom_10' => 'This is it',
      'custom_99' => 123,
      'custom_50' => 'data',
    ];
    $customValuesNew = [];
    foreach ($caseCustomFieldValues as $key => $customValue) {
      $customValuesNew['case_cf.' . $key] = $customValue;
    }

    $caseId = 1;
    $caseTokenValuesHelper = $this->getCaseTokenValuesHelperHelperMock($caseCustomFieldValues, $caseId);
    $addCaseCustomFieldsTokenValueHook = new AddCaseCustomFieldsTokenValueHook($caseTokenValuesHelper);

    $cid = 1;
    $values[$cid] = ['contact_id' => 5, 'display_name' => 'Sample'];
    $tokens['case_cf'] = array_merge(
      array_keys($caseCustomFieldValues),
      ['subject', 'name']
    );
    $expectedResult = array_merge($values[$cid], $customValuesNew);
    $addCaseCustomFieldsTokenValueHook->run($values, [$cid], 0, $tokens, NULL);

    $this->assertEquals($expectedResult, $values[$cid]);
  }

  /**
   * Test the values array is not tampered with.
   */
  public function testValuesNotChangedWhenCaseIdNotFound() {
    $caseCustomFieldValues = [
      'custom_10' => 'This is it',
    ];

    $caseCustomFieldValuesHelper = $this->getCaseTokenValuesHelperHelperMock($caseCustomFieldValues, NULL);
    $addCaseCustomFieldsTokenValueHook = new AddCaseCustomFieldsTokenValueHook($caseCustomFieldValuesHelper);

    $cid = 1;
    $expected = ['contact_id' => 5, 'display_name' => 'Sample'];
    $values[$cid] = $expected;
    $tokens['case'] = array_merge(
      array_keys($caseCustomFieldValues),
      ['subject', 'name']
    );
    $addCaseCustomFieldsTokenValueHook->run($values, [$cid], 0, $tokens, NULL);

    $this->assertEquals($expected, $values[$cid]);
  }

  /**
   * Get mock object for case token values helper.
   *
   * @param array $customFieldValues
   *   Custom field values to return.
   * @param int $caseId
   *   Case ID.
   *
   * @return \PHPUnit_Framework_MockObject_MockObject
   *   Mock object.
   */
  private function getCaseTokenValuesHelperHelperMock(array $customFieldValues, $caseId) {
    $caseCustomFieldValuesHelper = $this->getMockBuilder(CaseTokenValuesHelper::class)
      ->setMethods(
        [
          'getCustomFieldValues',
          'getCaseId',
          'getTokenReplacementValue',
        ]
      )
      ->getMock();
    $caseCustomFieldValuesHelper->method('getCustomFieldValues')->willReturn($customFieldValues);
    $caseCustomFieldValuesHelper->method('getCaseId')->willReturn($caseId);
    $returnValueMap = [];

    foreach ($customFieldValues as $customField => $customFieldValue) {
      $returnValueMap[] = [
        $customField,
        $customFieldValues,
        $customFieldValues[$customField],
      ];
    }

    $caseCustomFieldValuesHelper->method('getTokenReplacementValue')->will(
      $this->returnValueMap($returnValueMap)
    );

    return $caseCustomFieldValuesHelper;
  }

}
