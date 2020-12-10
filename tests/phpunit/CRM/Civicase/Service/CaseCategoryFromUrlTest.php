<?php

use CRM_Civicase_Service_CaseCategoryFromUrl as CategoryFromUrlService;
use CRM_Civicase_ExtensionUtil as ExtensionUtil;
use CRM_Civicase_Test_Fabricator_Contact as ContactFabricator;
use CRM_Civicase_Test_Fabricator_CaseType as CaseTypeFabricator;
use CRM_Civicase_Test_Fabricator_Case as CaseFabricator;
use CRM_Civicase_Test_Fabricator_CaseCategory as CaseCategoryFabricator;

/**
 * Test class for the CRM_Civicase_Service_CaseCategoryFromUrl.
 *
 * @group headless
 */
class CRM_Civicase_Service_CaseCategoryFromUrlTest extends BaseHeadlessTest {

  /**
   * Test get category name from case id in request.
   *
   * Test the detection of the category name, when it is received a case Id
   * on the request body.
   */
  public function testGetCategoryNameFromCaseIdInRequestBody() {
    $case = $this->createCase();
    $categoryFromUrlServiceMock = $this->mockRequestResponse([$case['id']]);
    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::CASE_TYPE_URL);

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals(
      $this->getCaseTypeCategoryNameByCaseTypeId($case['case_type_id']),
      $categoryName
    );
  }

  /**
   * Test get category name from case id in query string.
   *
   * Test the detection of the category name, when it is received a case id
   * on the query string of the URL.
   */
  public function testGetCategoryNameFromCaseIdInUrlQueryString() {
    $case = $this->createCase();

    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::CASE_TYPE_URL);
    $requestedUrlWithQueryString = $this->getUrlWithQueryString($requestedUrl, $case['id']);

    $categoryFromUrlServiceMock = $this->mockRequestResponse(
      [NULL, $requestedUrlWithQueryString]
    );

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals(
      $this->getCaseTypeCategoryNameByCaseTypeId($case['case_type_id']),
      $categoryName
    );
  }

  /**
   * Test get category name from request.
   *
   * Test the detection of the category name, when it is received that name
   * on the request body.
   */
  public function testGetCategoryNameFromRequestBodyContainingTheCategoryName() {
    $category = CaseCategoryFabricator::fabricate();
    $categoryFromUrlServiceMock = $this->mockRequestResponse([$category['name']]);
    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::CASE_CATEGORY_TYPE_URL);

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals($category['name'], $categoryName);
  }

  /**
   * Test get category name from category Id in request.
   *
   * Test the detection of the category name, when it is received the category
   * Id on the request body.
   */
  public function testGetCategoryNameFromRequestBodyContainingTheCategoryId() {
    $category = CaseCategoryFabricator::fabricate();
    $categoryFromUrlServiceMock = $this->mockRequestResponse([$category['value']]);
    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::CASE_CATEGORY_TYPE_URL);

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals($category['name'], $categoryName);
  }

  /**
   * Test get category name from query string.
   *
   * Test the detection of the category name, when it is received that name
   * on the query string of the URL.
   */
  public function testGetCategoryNameFromUrlQueryStringContainingTheCategoryName() {
    $category = CaseCategoryFabricator::fabricate();

    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::CASE_CATEGORY_TYPE_URL);
    $requestedUrlWithQueryString = $this->getUrlWithQueryString($requestedUrl, $category['name']);

    $categoryFromUrlServiceMock = $this->mockRequestResponse(
      [NULL, $requestedUrlWithQueryString]
    );

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals($category['name'], $categoryName);
  }

  /**
   * Test get category name from activity Id in request.
   *
   * Test the detection of the category name, when it is received an activity
   * Id on the request body.
   */
  public function testGetCategoryNameFromActivityIdInRequestBody() {
    $case = $this->createCase();
    $activity = $this->createActivityForCaseId($case['id']);
    $categoryFromUrlServiceMock = $this->mockRequestResponse([$activity['id']]);
    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::ACTIVITY_TYPE_URL);

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals(
      $this->getCaseTypeCategoryNameByCaseTypeId($case['case_type_id']),
      $categoryName
    );
  }

  /**
   * Test get category name from invalid activity Id in request.
   *
   * Test that NULL is return from the detection of the category name,
   * when it is received an invalid activity Id on the request body.
   */
  public function testGetCategoryNameFromInvalidActivityIdInRequestBody() {
    $categoryFromUrlServiceMock = $this->mockRequestResponse([rand()]);
    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::ACTIVITY_TYPE_URL);

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertNull($categoryName);
  }

  /**
   * Test get category name from category Id in case entity ajax request.
   *
   * Test the detection of the category name, when it is received the category
   * Id on an ajax request, related to case entity URL.
   */
  public function testGetCategoryNameFromAjaxRequestOfCaseEntityContainingTheCategoryId() {
    $category = CaseCategoryFabricator::fabricate();
    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::AJAX_TYPE_URL);
    $param = $this->getParamByUrl($requestedUrl);
    $categoryFromUrlServiceMock = $this->mockRequestResponse(
      [
        'case',
        json_encode([$param => $category['value']]),
      ]
    );

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals($category['name'], $categoryName);
  }

  /**
   * Test get category name from case entity ajax request.
   *
   * Test the detection of the category name, when it is received that category
   * name on an ajax request, related to case entity URL.
   */
  public function testGetCategoryNameFromAjaxRequestOfCaseEntityContainingTheCategoryName() {
    $category = CaseCategoryFabricator::fabricate();

    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::AJAX_TYPE_URL);
    $param = $this->getParamByUrl($requestedUrl);

    $categoryFromUrlServiceMock = $this->mockRequestResponse(
      [
        'case',
        json_encode([$param => $category['name']]),
      ]
    );

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals($category['name'], $categoryName);
  }

  /**
   * Test get category name from category Id in api3 ajax request.
   *
   * Test the detection of the category name, when it is received the category
   * Id on an ajax request, related to an api3 URL.
   */
  public function testGetCategoryNameFromAjaxRequestOfApi3ContainingTheCategoryId() {
    $category = CaseCategoryFabricator::fabricate();

    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::AJAX_TYPE_URL);
    $param = $this->getParamByUrl($requestedUrl);

    $categoryFromUrlServiceMock = $this->mockRequestResponse(
      [
        'api3',
        json_encode(
          [
            ['case', '', [$param => $category['value']]],
          ]
        ),
      ]
    );

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals($category['name'], $categoryName);
  }

  /**
   * Test get category name from api3 ajax request.
   *
   * Test the detection of the category name, when it is received that category
   * name on an ajax request, related to an api3 URL.
   */
  public function testGetCategoryNameFromAjaxRequestOfApi3ContainingTheCategoryName() {
    $category = CaseCategoryFabricator::fabricate();
    $requestedUrl = $this->getUrlByType(CategoryFromUrlService::AJAX_TYPE_URL);
    $param = $this->getParamByUrl($requestedUrl);

    $categoryFromUrlServiceMock = $this->mockRequestResponse(
      [
        'api3',
        json_encode(
          [
            ['case', '', [$param => $category['name']]],
          ]
        ),
      ]
    );

    $categoryName = $categoryFromUrlServiceMock->get($requestedUrl);

    $this->assertEquals($category['name'], $categoryName);
  }

  /**
   * Mock method getRequestResponse from the tested class.
   *
   * This method interacts with a static method from another class
   * (CRM_Utils_Request), then we mock the results for doing appropriate tests.
   *
   * @param array $responses
   *   Response/s that the mocked method will return.
   *
   * @return CRM_Civicase_Service_CaseCategoryFromUrl|PHPUnit_Framework_MockObject_MockObject
   *   Mocked instance of CategoryFromUrlService.
   */
  private function mockRequestResponse(array $responses) {
    $categoryFromUrlServiceMock = $this->getMockBuilder(CategoryFromUrlService::class)
      ->setMethods(['getRequestResponse'])
      ->getMock();
    $categoryFromUrlServiceMock
      ->expects($this->exactly(count($responses)))
      ->method('getRequestResponse')
      ->willReturnOnConsecutiveCalls(...$responses);

    return $categoryFromUrlServiceMock;
  }

  /**
   * Returns the URL's array configurations.
   *
   * @return array
   *   The URLs config array.
   */
  private function getUrlsConfig() {
    $configFile = CRM_Core_Resources::singleton()
      ->getPath(ExtensionUtil::LONG_NAME, 'config/urls/case_category_url.php');

    return include $configFile;
  }

  /**
   * Find a Url of the type specified.
   *
   * @param string $urlType
   *   Type of URL that the test service can handle.
   *
   * @return string|null
   *   Example URL for testing the service response.
   */
  private function getUrlByType(string $urlType) {
    $configs = $this->getUrlsConfig();

    foreach ($configs as $url => $config) {
      if ($config['url_type'] == $urlType) {
        return $url;
      }
    }

    return NULL;
  }

  /**
   * Generate the full Url, with the appropriate query string.
   *
   * The value for the param is given as an argument, while the name of the
   * param is determined by a conf. file, and returned in getParamByUrl method.
   *
   * @param string $requestedUrl
   *   Url to add the query string value.
   * @param string|int $valueForParam
   *   Value to be added for the param.
   *
   * @return string
   *   Url with the query string appended.
   */
  private function getUrlWithQueryString(string $requestedUrl, $valueForParam) {
    $param = $this->getParamByUrl($requestedUrl);
    $requestedUrl = (parse_url($requestedUrl, PHP_URL_QUERY) ? '&' : '?');
    $requestedUrl .= $param . '=' . $valueForParam;

    return $requestedUrl;
  }

  /**
   * Return the param name associated with the Url received.
   *
   * @param string $url
   *   Url that can be handled by the service.
   *
   * @return string
   *   Name of the param that contains the information on the Url.
   */
  private function getParamByUrl(string $url) {
    $configs = $this->getUrlsConfig();

    return $configs[$url]['param'];
  }

  /**
   * Create a case.
   *
   * @return mixed
   *   Case details.
   */
  private function createCase() {
    $caseType = CaseTypeFabricator::fabricate();
    $contact = ContactFabricator::fabricate();

    return CaseFabricator::fabricate(
      [
        'case_type_id' => $caseType['id'],
        'contact_id' => $contact['id'],
        'creator_id' => $contact['id'],
      ]
    );
  }

  /**
   * Find the category name associated with the case type specified.
   *
   * @param int $caseTypeId
   *   Id of the case type.
   *
   * @return string
   *   The category name.
   */
  private function getCaseTypeCategoryNameByCaseTypeId($caseTypeId) {
    return civicrm_api3('CaseType', 'getsingle', [
      'id' => $caseTypeId,
      'return' => ['case_type_category.name'],
    ])['case_type_category.name'];
  }

  /**
   * Creates an activity related to the case specified.
   *
   * @param int $caseId
   *   Id of the case.
   *
   * @return array
   *   Activity details.
   */
  private function createActivityForCaseId($caseId) {
    $contact = ContactFabricator::fabricate();

    return civicrm_api3('Activity', 'create', [
      'case_id' => $caseId,
      'source_contact_id' => $contact['id'],
      'activity_type_id' => 'Assign Case Role',
    ]);
  }

}
