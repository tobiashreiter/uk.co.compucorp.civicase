((_) => {
  describe('Contact Case Tab Case Details', () => {
    let $controller, $rootScope, $scope, CaseTypeCategory, mockCase,
      civicaseCrmUrl;

    beforeEach(module('civicase', 'civicase.data'));

    beforeEach(inject((_$controller_, _$rootScope_, _CasesData_,
      _CaseTypeCategory_, _civicaseCrmUrl_) => {
      $controller = _$controller_;
      $rootScope = _$rootScope_;
      CaseTypeCategory = _CaseTypeCategory_;
      mockCase = _CasesData_.get().values[0];
      civicaseCrmUrl = _civicaseCrmUrl_;

      initController();
    }));

    describe('when requesting the case details page URL', () => {
      var caseTypeCategory;

      beforeEach(() => {
        caseTypeCategory = _.chain(CaseTypeCategory.getAll())
          .values()
          .sample()
          .value();
        mockCase['case_type_id.case_type_category'] = caseTypeCategory.value;
        $scope.getCaseDetailsUrl(mockCase);
      });

      it('returns the case details page url for the given case', () => {
        expect(civicaseCrmUrl).toHaveBeenCalledWith('civicrm/case/a/',
          `case_type_category=${caseTypeCategory.value}` +
          `#/case/list?caseId=${mockCase.id}&focus=1&cf={"id":${mockCase.id}}`);
      });
    });

    /**
     * Initializes the contact case tab case details controller.
     */
    function initController () {
      $scope = $rootScope.$new();

      $controller('CivicaseContactCaseTabCaseDetailsController', { $scope: $scope });
    }
  });
})(CRM._);
