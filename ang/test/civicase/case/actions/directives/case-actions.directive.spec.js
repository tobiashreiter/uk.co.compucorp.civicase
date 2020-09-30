/* eslint-env jasmine */

(function (_) {
  describe('case actions', function () {
    beforeEach(module('civicase', 'civicase.data', 'civicase.templates'));

    let element, $compile, $rootScope, CaseActionsData;

    beforeEach(inject(function (_$compile_, _$rootScope_, _CaseActionsData_) {
      $compile = _$compile_;
      $rootScope = _$rootScope_;
      CaseActionsData = _CaseActionsData_;
    }));

    describe('basic tests', () => {
      beforeEach(() => {
        compileDirective();
      });

      it('compiles the case action directive', () => {
        expect(element.html()).toContain('ng-repeat="action in caseActions');
      });
    });

    describe('sub menus', () => {
      var action;

      describe('when the menu has sub items', function () {
        beforeEach(() => {
          compileDirective();

          action = _.find(CaseActionsData.get(), (action) => {
            return action.items;
          });
        });

        it('shows the submenu', () => {
          expect(element.isolateScope().hasSubMenu(action)).toBe(true);
        });
      });

      describe('when the menu does not have sub items', function () {
        beforeEach(() => {
          compileDirective();

          action = _.find(CaseActionsData.get(), (action) => {
            return !action.items;
          });
        });

        it('hides the submenu', () => {
          expect(element.isolateScope().hasSubMenu(action)).toBe(false);
        });
      });
    });

    describe('disabling of the action', () => {
      var action;

      describe('when action can only be enabled for any number of cases', function () {
        beforeEach(() => {
          compileDirective();

          action = _.find(CaseActionsData.get(), (action) => {
            return !action.number;
          });
        });

        it('enables the action', () => {
          expect(element.isolateScope().isActionEnabled(action)).toBe(true);
        });
      });

      describe('when action can only be enabled for a defined number of cases and number of cases match', function () {
        beforeEach(() => {
          compileDirective();

          action = _.find(CaseActionsData.get(), (action) => {
            return action.number;
          });
          element.isolateScope().cases = [{ id: 1 }];
        });

        it('enables the action', () => {
          expect(element.isolateScope().isActionEnabled(action)).toBe(true);
        });
      });

      describe('when action can only be enabled for a defined number of cases and number of cases does not match', function () {
        beforeEach(() => {
          compileDirective();

          action = _.find(CaseActionsData.get(), (action) => {
            return action.number;
          });
          element.isolateScope().cases = [{ id: 1 }, { id: 2 }];
        });

        it('disables the action', () => {
          expect(element.isolateScope().isActionEnabled(action)).toBe(false);
        });
      });
    });

    // TODO: FINISH REST of the unit test

    /**
     * Compiles the directive
     */
    function compileDirective () {
      element = $compile('<div civicase-case-actions=[]></div>')($rootScope);
      $rootScope.$digest();
    }
  });
})(CRM._);
