/* eslint-env jasmine */

(($, loadForm, getCrmUrl) => {
  describe('AddCaseService', () => {
    let $window, AddCase, mockedFormPopUp, CaseCategoryWebformSettings;

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

          isButtonVisible = AddCase.isVisible();
        });

        it('displays the button', () => {
          expect(isButtonVisible).toBe(true);
        });
      });

      describe('when the user cannot add new cases', () => {
        beforeEach(() => {
          CRM.checkPerm.and.returnValue(false);

          isButtonVisible = AddCase.isVisible();
        });

        it('does not display the button', () => {
          expect(isButtonVisible).toBe(false);
        });
      });
    });

    describe('click handler', () => {
      beforeEach(module('civicase', ($provide) => {
        CaseCategoryWebformSettings = jasmine.createSpyObj('CaseCategoryWebformSettings', ['getSettingsFor']);
        $provide.value('CaseCategoryWebformSettings', CaseCategoryWebformSettings);
      }));

      describe('when the new case web form url configuration value is defined', () => {
        beforeEach(module('civicase', ($provide) => {
          $provide.value('$window', $window);
          CaseCategoryWebformSettings.getSettingsFor.and.returnValue({
            newCaseWebformUrl: '/someurl',
            newCaseWebformClient: 'cid'
          });
        }));

        beforeEach(() => {
          injectDependencies();
          AddCase.clickHandler('case', '5');
        });

        it('redirects the user to the configured web form url value', () => {
          expect($window.location.href).toBe('/someurl?cid=5');
        });
      });

      describe('when the new case web form url configuration value is not defined', () => {
        beforeEach(module('civicase-base', 'civicase', ($provide) => {
          CaseCategoryWebformSettings.getSettingsFor.and.returnValue({ newCaseWebformUrl: null });
          $provide.value('$window', $window);
        }));

        beforeEach(() => {
          injectDependencies();
          mockFormPopUpDom();
          AddCase.clickHandler();
        });

        it('does not redirect the user', () => {
          expect($window.location.href).toBe('');
        });
      });

      describe('opening a new case form popup', () => {
        let expectedFormUrl;

        beforeEach(module('civicase', 'civicase.data', ($provide) => {
          CaseCategoryWebformSettings.getSettingsFor.and.returnValue({ newCaseWebformUrl: null });
        }));

        beforeEach(() => {
          injectDependencies();
          mockFormPopUpDom();

          expectedFormUrl = getCrmUrl('civicrm/case/add', {
            action: 'add',
            case_type_category: 'case',
            civicase_cid: '5',
            context: 'standalone',
            reset: 1
          });

          AddCase.clickHandler('case', '5');
        });

        it('opens the new case form', () => {
          expect(loadForm).toHaveBeenCalledWith(expectedFormUrl);
        });
      });
    });

    /**
     * Injects and hoists the dependencies used by this spec file.
     */
    function injectDependencies () {
      inject((_$window_, _AddCase_) => {
        $window = _$window_;
        AddCase = _AddCase_;
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
