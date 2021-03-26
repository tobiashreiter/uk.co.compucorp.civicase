describe('Case Details People Tab', () => {
  let $q, $controller, $rootScope, $scope, CasesData, CaseTypesMockData,
    civicaseCrmApiMock, OtherRelationshipsData;

  beforeEach(module('civicase', 'civicase.data'));

  beforeEach(module('civicase', 'civicase.data', ($provide) => {
    civicaseCrmApiMock = jasmine.createSpy('civicaseCrmApi');

    $provide.value('civicaseCrmApi', civicaseCrmApiMock);
  }));

  beforeEach(inject(function (_$q_, _$controller_, _$rootScope_,
    _CasesData_, _CaseTypesMockData_, _OtherRelationshipsData_) {
    $q = _$q_;
    $controller = _$controller_;
    $rootScope = _$rootScope_;
    CasesData = _CasesData_;
    OtherRelationshipsData = _OtherRelationshipsData_;
    CaseTypesMockData = _CaseTypesMockData_;

    $scope = $rootScope.$new();

    civicaseCrmApiMock.and.returnValue($q.resolve([
      { values: OtherRelationshipsData.get() },
      { count: OtherRelationshipsData.get().length }
    ]));
  }));

  beforeEach(() => {
    const caseType = CaseTypesMockData.get()[1];
    $scope.item = CasesData.get().values[0];
    $scope.item.definition = caseType.definition;

    initController({
      $scope: $scope
    });
  });

  describe('on init', () => {
    it('initialises empty list of relations before fetching', () => {
      expect($scope.relations).toEqual([]);
    });

    it('initialises pagination', () => {
      expect($scope.relationsPageObj).toEqual({ total: 0, pageSize: 25, page: 1 });
    });

    it('does not apply any initial filters', () => {
      expect($scope.relationsFilter).toEqual({ alpha: '' });
    });

    it('initialises the bulk action', () => {
      expect($scope.relationsSelectionMode).toBe('');
      expect($scope.relationsSelectedTask).toBe('');
    });

    it('shows loading screen', () => {
      expect($scope.isRelationshipLoading).toBe(true);
    });
  });

  describe('when clicking on a pagination item', () => {
    beforeEach(() => {
      $scope.relationsFilter = { alpha: 'Z' };
      $scope.goToPage(2);
    });

    it('displays the records for the clicked page', () => {
      expect($scope.relationsPageObj.page).toBe(2);
      expect(civicaseCrmApiMock).toHaveBeenCalledWith([
        ['Case', 'getrelations', jasmine.objectContaining({
          case_id: '141',
          sequential: 1,
          return: ['display_name', 'phone', 'email'],
          display_name: 'Z',
          options: {
            limit: 25,
            offset: 25
          }
        })],
        ['Case', 'getrelationscount', jasmine.any(Object)]
      ]);
    });
  });

  describe('when loading the relations', () => {
    beforeEach(() => {
      $scope.relationsFilter = { alpha: 'Z' };
      $scope.getRelations($scope.relationsFilter);
      $scope.$digest();
    });

    it('sets the paginations', () => {
      expect($scope.relationsPageObj.total).toBe(2);
    });

    it('hides the loader', () => {
      expect($scope.isRelationshipLoading).toBe(false);
    });

    it('displays all the relations', () => {
      expect($scope.relations).toEqual([
        jasmine.objectContaining({ relation: 'Application Manager is', client: 'Kiara Adams' }),
        jasmine.objectContaining({ relation: 'Benefits Specialist is', client: 'Kiara Adams' })
      ]);
    });
  });

  describe('when loading the relations', () => {
    beforeEach(() => {
      $scope.relationsFilter = { alpha: 'Z' };
      $scope.setLetterFilter($scope.relationsFilter);
      $scope.$digest();
    });

    it('sets the paginations', () => {
      expect($scope.relationsPageObj.total).toBe(2);
      expect($scope.relationsPageObj.page).toBe(1);
    });

    it('hides the loader', () => {
      expect($scope.isRelationshipLoading).toBe(false);
    });

    it('displays all the relations', () => {
      expect($scope.relations).toEqual([
        jasmine.objectContaining({ relation: 'Application Manager is', client: 'Kiara Adams' }),
        jasmine.objectContaining({ relation: 'Benefits Specialist is', client: 'Kiara Adams' })
      ]);
    });
  });

  /**
   * Initializes the controller.
   *
   * @param {object} dependencies the list of dependencies to pass to the controller.
   */
  function initController (dependencies) {
    $controller('civicaseOtherRelationshipsTabController', dependencies);
  }
});
