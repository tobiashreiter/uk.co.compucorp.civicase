/* eslint-env jasmine */

(($) => {
  describe('AddCaseDashboardActionButtonController', () => {
    let $location, $rootScope, $scope, $controller, AddCase;

    beforeEach(module('civicase-base', 'civicase'));

    describe('Button Visibility', () => {
      beforeEach(() => {
        injectDependencies();
        spyOn(AddCase, 'isVisible');
        initController();

        $scope.isVisible();
      });

      it('displays the Add Case button only when adequate permission is available', () => {
        expect(AddCase.isVisible).toHaveBeenCalled();
      });
    });

    describe('Click Event', () => {
      beforeEach(() => {
        injectDependencies();
        spyOn(AddCase, 'clickHandler');
        spyOn($location, 'search').and.returnValue({
          case_type_category: 'cases'
        });
        initController();

        $scope.clickHandler();
      });

      it('creates a new case', () => {
        expect(AddCase.clickHandler).toHaveBeenCalledWith({
          caseTypeCategoryName: 'cases'
        });
      });
    });

    /**
     * Initializes the contact case tab case details controller.
     */
    function initController () {
      $scope = $rootScope.$new();

      $controller('AddCaseDashboardActionButtonController', { $scope: $scope });
    }

    /**
     * Injects and hoists the dependencies used by this spec file.
     */
    function injectDependencies () {
      inject((_$location_, _$rootScope_, _$controller_, _AddCase_) => {
        $location = _$location_;
        $controller = _$controller_;
        $rootScope = _$rootScope_;
        AddCase = _AddCase_;
      });
    }
  });
})(CRM.$);
