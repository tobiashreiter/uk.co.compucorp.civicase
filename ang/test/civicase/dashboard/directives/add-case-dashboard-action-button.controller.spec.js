/* eslint-env jasmine */

(($, loadForm, getCrmUrl) => {
  describe('AddCaseDashboardActionButtonController', () => {
    let $location, $window, $rootScope, $scope, $controller, currentCaseCategory,
      mockedFormPopUp;

    beforeEach(() => {
      $window = { location: { href: '' } };
    });

    describe('Button Visibility', () => {
      let isButtonVisible;

      beforeEach(module('civicase-base', 'civicase'));
      beforeEach(() => {
        injectDependencies();
      });

      describe('when the user can add new cases', () => {
        beforeEach(() => {
          CRM.checkPerm.and.returnValue(true);

          isButtonVisible = $scope.isVisible();
        });

        it('displays the button', () => {
          expect(isButtonVisible).toBe(true);
        });
      });

      describe('when the user cannot add new cases', () => {
        beforeEach(() => {
          CRM.checkPerm.and.returnValue(false);

          isButtonVisible = $scope.isVisible();
        });

        it('does not display the button', () => {
          expect(isButtonVisible).toBe(false);
        });
      });
    });

    describe('click handler', () => {
      describe('when the new case web form url configuration value is defined', () => {
        const newCaseWebformUrl = 'http://example.com/';

        beforeEach(module('civicase', ($provide) => {
          $provide.constant('newCaseWebformUrl', newCaseWebformUrl);
          $provide.value('$window', $window);
        }));

        beforeEach(() => {
          injectDependencies();
          $scope.clickHandler();
        });

        it('redirects the user to the configured web form url value', () => {
          expect($window.location.href).toBe(newCaseWebformUrl);
        });
      });

      describe('when the new case web form url configuration value is not defined', () => {
        beforeEach(module('civicase-base', 'civicase', ($provide) => {
          $provide.constant('newCaseWebformUrl', null);
          $provide.value('$window', $window);
        }));

        beforeEach(() => {
          injectDependencies();
          mockFormPopUpDom();
          $scope.clickHandler();
        });

        it('does not redirect the user', () => {
          expect($window.location.href).toBe('');
        });
      });

      describe('opening a new case form popup', () => {
        let expectedFormUrl;

        beforeEach(module('civicase', 'civicase.data'));

        beforeEach(() => {
          injectDependencies();
          mockFormPopUpDom();

          spyOn($location, 'search').and.returnValue({});
        });

        describe('when creating a new case from the default cases dashboard', () => {
          beforeEach(() => {
            expectedFormUrl = getCrmUrl('civicrm/case/add', {
              action: 'add',
              case_type_category: currentCaseCategory,
              context: 'standalone',
              reset: 1
            });

            $scope.clickHandler();
          });

          it('opens the new case form for the default case type category', () => {
            expect(loadForm).toHaveBeenCalledWith(expectedFormUrl);
          });
        });

        describe('when creating a new case from the dashboard of a custom case type category', () => {
          const caseTypeCategory = 'traveling';

          beforeEach(() => {
            expectedFormUrl = getCrmUrl('civicrm/case/add', {
              action: 'add',
              case_type_category: caseTypeCategory,
              context: 'standalone',
              reset: 1
            });

            $location.search.and.returnValue({
              case_type_category: caseTypeCategory
            });
            $scope.clickHandler();
          });

          it('opens the new case form for the custom case type category', () => {
            expect(loadForm).toHaveBeenCalledWith(expectedFormUrl);
          });
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
      inject((_$location_, _$rootScope_, _$window_, _$controller_,
        _currentCaseCategory_) => {
        $location = _$location_;
        $window = _$window_;
        $controller = _$controller_;
        $rootScope = _$rootScope_;
        currentCaseCategory = _currentCaseCategory_;

        initController();
      });
    }

    /**
     * Creates a mocked popup element that will be returned by the load form function.
     */
    function mockFormPopUpDom () {
      mockedFormPopUp = $('<div></div>');

      loadForm.and.returnValue(mockedFormPopUp);
    }
  });
})(CRM.$, CRM.loadForm, CRM.url);
