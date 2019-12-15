/* eslint-env jasmine */

(($, loadForm, getCrmUrl) => {
  describe('AddCaseDashboardActionButton', () => {
    let $rootScope, $location, AddCaseDashboardActionButton, defaultCaseCategory,
      mockedFormPopUp;

    describe('Button Visibility', () => {
      let isButtonVisible;

      beforeEach(module('civicase-base', 'civicase'));
      beforeEach(injectDependencies);

      describe('when the user can add new cases', () => {
        beforeEach(() => {
          CRM.checkPerm.and.returnValue(true);

          isButtonVisible = AddCaseDashboardActionButton.isVisible();
        });

        it('displays the button', () => {
          expect(isButtonVisible).toBe(true);
        });
      });

      describe('when the user cannot add new cases', () => {
        beforeEach(() => {
          CRM.checkPerm.and.returnValue(false);

          isButtonVisible = AddCaseDashboardActionButton.isVisible();
        });

        it('does not display the button', () => {
          expect(isButtonVisible).toBe(false);
        });
      });
    });

    describe('click handler', () => {
      describe('when the new case web form url configuration value is defined', () => {
        let expectedRedirectedUrl;
        const newCaseWebformUrl = 'http://example.com/';

        beforeEach(module('civicase', ($provide) => {
          $provide.constant('newCaseWebformUrl', newCaseWebformUrl);
        }));

        beforeEach(() => {
          expectedRedirectedUrl = getCrmUrl(newCaseWebformUrl);

          injectDependencies();
          spyOn($location, 'url');
          AddCaseDashboardActionButton.clickHandler();
        });

        it('redirects the user to the configured web form url value', () => {
          expect($location.url).toHaveBeenCalledWith(expectedRedirectedUrl);
        });
      });

      describe('when the new case web form url configuration value is not defined', () => {
        beforeEach(module('civicase-base', 'civicase', ($provide) => {
          $provide.constant('newCaseWebformUrl', null);
        }));

        beforeEach(() => {
          injectDependencies();
          mockFormPopUpDom();
          spyOn($location, 'url');
          AddCaseDashboardActionButton.clickHandler();
        });

        it('does not redirect the user', () => {
          expect($location.url).not.toHaveBeenCalled();
        });
      });

      describe('opening a new case form popup', () => {
        let expectedFormUrl;

        beforeEach(module('civicase', 'civicase.data'));

        beforeEach(() => {
          injectDependencies();
          mockFormPopUpDom();

          spyOn($location, 'search').and.returnValue({});
          spyOn($rootScope, '$emit');
        });

        describe('when creating a new case from the default cases dashboard', () => {
          beforeEach(() => {
            expectedFormUrl = getCrmUrl('civicrm/case/add', {
              action: 'add',
              case_type_category: defaultCaseCategory,
              context: 'standalone',
              reset: 1
            });

            AddCaseDashboardActionButton.clickHandler();
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
            AddCaseDashboardActionButton.clickHandler();
          });

          it('opens the new case form for the custom case type category', () => {
            expect(loadForm).toHaveBeenCalledWith(expectedFormUrl);
          });
        });
      });
    });

    /**
     * Injects and hoists the dependencies used by this spec file.
     */
    function injectDependencies () {
      inject((_$location_, _$rootScope_, _AddCaseDashboardActionButton_,
        _defaultCaseCategory_) => {
        $location = _$location_;
        $rootScope = _$rootScope_;
        AddCaseDashboardActionButton = _AddCaseDashboardActionButton_;
        defaultCaseCategory = _defaultCaseCategory_;
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
