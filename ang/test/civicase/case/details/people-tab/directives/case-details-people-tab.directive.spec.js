describe('Case Details People Tab', () => {
  let $controller, $rootScope, $scope, CasesData, CaseTypesMockData;

  beforeEach(module('civicase', 'civicase.data'));

  beforeEach(inject(function (_$controller_, _$q_, _$rootScope_,
    _CasesData_, _CaseTypesMockData_) {
    $controller = _$controller_;
    $rootScope = _$rootScope_;
    CasesData = _CasesData_;
    CaseTypesMockData = _CaseTypesMockData_;

    $scope = $rootScope.$new();
    $scope.$bindToRoute = jasmine.createSpy('$bindToRoute');
  }));

  beforeEach(() => {
    const caseType = CaseTypesMockData.get()[1];
    $scope.item = CasesData.get().values[0];
    $scope.item.definition = caseType.definition;

    initController({
      $scope: $scope
    });
  });

  describe('when clicking on a tab', () => {
    beforeEach(() => {
      $scope.setTab('roles');
    });

    it('sets the clicked tab as visible', () => {
      expect($scope.tab).toBe('roles');
    });
  });

  /**
   * Initializes the controller.
   *
   * @param {object} dependencies the list of dependencies to pass to the controller.
   */
  function initController (dependencies) {
    $controller('civicaseViewPeopleController', dependencies);
  }
});
