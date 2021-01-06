/* eslint-env jasmine */

((_) => {
  describe('case management workflow', () => {
    let $q, $rootScope, $window, civicaseCrmApiMock, CaseTypesMockData,
      CaseManagementWorkflow;

    beforeEach(module('workflow', 'civicase.data', ($provide) => {
      civicaseCrmApiMock = jasmine.createSpy('civicaseCrmApi');

      $provide.value('civicaseCrmApi', civicaseCrmApiMock);
      $provide.value('$window', { location: {} });
    }));

    beforeEach(inject((_$q_, _$rootScope_, _$window_, _CaseManagementWorkflow_,
      _CaseTypesMockData_) => {
      $q = _$q_;
      $rootScope = _$rootScope_;
      $window = _$window_;
      CaseManagementWorkflow = _CaseManagementWorkflow_;
      CaseTypesMockData = _CaseTypesMockData_;
    }));

    describe('when getting list of workflow', () => {
      var results;

      beforeEach(() => {
        civicaseCrmApiMock.and.returnValue($q.resolve({
          values: CaseTypesMockData.getSequential()
        }));

        CaseManagementWorkflow.getWorkflowsList('some_case_type_category')
          .then(function (data) {
            results = data;
          });
        $rootScope.$digest();
      });

      it('fetches the workflows for the case management instance', () => {
        expect(civicaseCrmApiMock).toHaveBeenCalledWith('CaseType', 'get', {
          sequential: 1,
          case_type_category: 'some_case_type_category',
          options: { limit: 0 }
        });
      });

      it('displays the list of fetched workflows', () => {
        expect(results).toEqual(CaseTypesMockData.getSequential());
      });
    });

    describe('when duplicating a workflow', () => {
      var workflow;

      beforeEach(() => {
        workflow = CaseTypesMockData.getSequential()[0];
        CaseManagementWorkflow.createDuplicate(workflow);
      });

      it('creates a duplicate workflow', () => {
        expect(civicaseCrmApiMock).toHaveBeenCalledWith([
          ['CaseType', 'create', _.extend({}, workflow, { id: null })]
        ]);
      });
    });

    describe('when redirecting to the create workflow page', () => {
      beforeEach(() => {
        CaseManagementWorkflow.redirectToWorkflowCreationScreen();
      });

      it('redirects to the case management new workflow page', () => {
        expect($window.location.href).toBe('/civicrm/a/#/caseType/new');
      });
    });

    describe('when editing a workflow', () => {
      var returnValue;

      beforeEach(() => {
        var workflow = CaseTypesMockData.getSequential()[0];

        returnValue = CaseManagementWorkflow.getEditWorkflowURL(workflow);
      });

      it('redirects to the case type page for the clicked workflow', () => {
        expect(returnValue).toBe('civicrm/a/#/caseType/1');
      });
    });
  });
})(CRM._);
