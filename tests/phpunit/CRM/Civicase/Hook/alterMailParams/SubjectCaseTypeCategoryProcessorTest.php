<?php

use CRM_Civicase_Hook_alterMailParams_SubjectCaseTypeCategoryProcessor as SubjectCaseTypeCategoryProcessor;
use CRM_Civicase_Test_Fabricator_CaseCategoryInstance as CaseCategoryInstanceFabricator;
use CRM_Civicase_Test_Fabricator_CaseCategory as CaseCategoryFabricator;
use CRM_Civicase_Test_Fabricator_CaseCategoryInstanceType as CaseCategoryInstanceTypeFabricator;
use CRM_Civicase_Service_CaseCategoryCustomFieldsSetting as CaseCategoryCustomFieldsSetting;
use CRM_Civicase_Test_Fabricator_CaseType as CaseTypeFabricator;
use CRM_Civicase_Test_Fabricator_Case as CaseFabricator;
use CRM_Civicase_Test_Fabricator_Contact as ContactFabricator;

/**
 * Test class for the SubjectCaseTypeCategoryProcessor.
 *
 * @group headless
 */
class CRM_Civicase_Hook_alterMailParams_SubjectCaseTypeCategoryProcessorTest extends BaseHeadlessTest {

  /**
   * Test first instance of case is replaced.
   */
  public function testRunReplacesTheFirstInstanceOfCaseInMailSubjectCorrectly() {
    $categoryInstanceTypeOne = CaseCategoryInstanceTypeFabricator::fabricate();
    $categoryParams = ['label' => 'Awards', 'singular_label' => 'Award'];
    $caseCategory = $this->createCaseTypeCategory($categoryParams);
    CaseCategoryInstanceFabricator::fabricate(
      [
        'category_id' => $caseCategory['value'],
        'instance_id' => $categoryInstanceTypeOne['value'],
      ]
    );

    $emailSubjectProcessor = new SubjectCaseTypeCategoryProcessor();
    $_REQUEST['caseid'] = $this->getCase($caseCategory['value'])['id'];
    $params['subject'] = "[case ] This is a test email subject case";
    $emailSubjectProcessor->run($params, $context = '');
    $replacedValue = strtolower($categoryParams['singular_label']);
    $expectedSubject = "[{$replacedValue} ] This is a test email subject case";
    $this->assertEquals($expectedSubject, $params['subject']);
  }

  /**
   * Fabricates a case type category.
   *
   * @param array $caseTypeCategoryParams
   *   Case type category params.
   *
   * @return array
   *   Case category.
   */
  private function createCaseTypeCategory(array $caseTypeCategoryParams) {
    $caseTypeCategory = CaseCategoryFabricator::fabricate($caseTypeCategoryParams);
    (new CaseCategoryCustomFieldsSetting())->save(
      $caseTypeCategory['value'], ['singular_label' => $caseTypeCategoryParams['singular_label']]
    );

    return $caseTypeCategory;
  }

  /**
   * Fabricates a case with given case category.
   *
   * @param int $caseTypeCategory
   *   Case type category value.
   *
   * @return array
   *   Case data.
   */
  private function getCase($caseTypeCategory) {
    $caseType = CaseTypeFabricator::fabricate(['case_type_category' => $caseTypeCategory]);
    $client = ContactFabricator::fabricate();

    return CaseFabricator::fabricate(
      [
        'case_type_id' => $caseType['id'],
        'contact_id' => $client['id'],
        'creator_id' => $client['id'],
      ]
    );
  }

}
