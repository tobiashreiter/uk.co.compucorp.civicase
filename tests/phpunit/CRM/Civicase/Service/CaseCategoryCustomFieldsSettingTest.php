<?php

use CRM_Civicase_Service_CaseCategoryCustomFieldsSetting as CaseCategoryCustomFieldsSetting;

/**
 * Test class for the case category custom fields setting service.
 *
 * @group headless
 */
class CRM_Civicase_Service_CaseCategoryCustomFieldsSettingTest extends BaseHeadlessTest {

  /**
   * Test saving case category custom fields.
   */
  public function testSaveCustomFields() {
    $caseCategoryId = uniqid();
    $caseCategoryCustomFields = new CaseCategoryCustomFieldsSetting();
    $fieldData = [
      'my_custom_field' => 'value',
    ];

    $caseCategoryCustomFields->save($caseCategoryId, $fieldData);

    $caseCategoryCustomFieldsValues = $this->getCaseCategoryCustomFieldsValues();

    $this->assertArrayHasKey($caseCategoryId, $caseCategoryCustomFieldsValues);
    $this->assertEquals($caseCategoryCustomFieldsValues[$caseCategoryId], $fieldData);
  }

  /**
   * Test getting case category custom fields.
   */
  public function testGettingCustomFields() {
    $caseCategoryId1 = uniqid();
    $caseCategoryId2 = uniqid();
    $caseCategoryId3 = uniqid();
    $caseCategoryCustomFields = new CaseCategoryCustomFieldsSetting();

    $caseCategoryCustomFields->save($caseCategoryId1, [
      'my_custom_field_a' => 'valuea1',
      'my_custom_field_b' => 'valueb1',
    ]);
    $caseCategoryCustomFields->save($caseCategoryId2, [
      'my_custom_field_a' => 'valuea2',
      'my_custom_field_b' => 'valueb2',
    ]);

    $caseCategory1Values = $caseCategoryCustomFields->get($caseCategoryId1);
    $caseCategory2Values = $caseCategoryCustomFields->get($caseCategoryId2);
    $caseCategory3Values = $caseCategoryCustomFields->get($caseCategoryId3);

    $expectedValues1 = [
      'my_custom_field_a' => 'valuea1',
      'my_custom_field_b' => 'valueb1',
    ];
    $expectedValues2 = [
      'my_custom_field_a' => 'valuea2',
      'my_custom_field_b' => 'valueb2',
    ];

    $this->assertEquals($expectedValues1, $caseCategory1Values);
    $this->assertEquals($expectedValues2, $caseCategory2Values);
    $this->assertEquals(NULL, $caseCategory3Values);
  }

  /**
   * Test deleting case category custom fields.
   */
  public function testDeleteCustomFields() {
    $caseCategoryId1 = uniqid();
    $caseCategoryId2 = uniqid();
    $caseCategoryCustomFields = new CaseCategoryCustomFieldsSetting();

    $caseCategoryCustomFields->save($caseCategoryId1, [
      'my_custom_field_a' => 'valuea1',
      'my_custom_field_b' => 'valueb1',
    ]);
    $caseCategoryCustomFields->save($caseCategoryId2, [
      'my_custom_field_a' => 'valuea2',
      'my_custom_field_b' => 'valueb2',
    ]);
    $caseCategoryCustomFields->delete($caseCategoryId1);

    $caseCategory1Values = $caseCategoryCustomFields->get($caseCategoryId1);
    $caseCategory2Values = $caseCategoryCustomFields->get($caseCategoryId2);

    $expectedValues2 = [
      'my_custom_field_a' => 'valuea2',
      'my_custom_field_b' => 'valueb2',
    ];

    $this->assertEquals(NULL, $caseCategory1Values);
    $this->assertEquals($expectedValues2, $caseCategory2Values);
  }

  /**
   * Returns the case category custom fields settings value.
   *
   * @return array
   *   Case category custom field settings value.
   */
  private function getCaseCategoryCustomFieldsValues() {
    return Civi::settings()->get(CaseCategoryCustomFieldsSetting::SETTING_NAME);
  }

}
