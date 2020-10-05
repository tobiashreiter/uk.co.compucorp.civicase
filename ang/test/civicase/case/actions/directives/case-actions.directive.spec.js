/* eslint-env jasmine */

(function (_) {
  describe('case actions', function () {
    let element, $provide, $compile, $rootScope, CaseActionsData;

    beforeEach(module('civicase', 'civicase.data', 'civicase.templates', (_$provide_) => {
      $provide = _$provide_;
    }));

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

    describe('visibility of the action', () => {
      var action;

      describe('when lock cases action', () => {
        describe('and cases can be locked', () => {
          beforeEach(() => {
            $provide.constant('allowCaseLocks', true);
            compileDirective();

            action = _.find(CaseActionsData.get(), (action) => {
              return action.action === 'LockCases';
            });
          });

          it('shows the action', () => {
            expect(element.isolateScope().isActionAllowed(action)).toBe(true);
          });
        });

        describe('and cases can not be locked', () => {
          beforeEach(() => {
            $provide.constant('allowCaseLocks', false);
            compileDirective();

            action = _.find(CaseActionsData.get(), (action) => {
              return action.action === 'LockCases';
            });
          });

          it('hides the action', () => {
            expect(element.isolateScope().isActionAllowed(action)).toBe(false);
          });
        });
      });

      describe('when not lock cases action', () => {
        describe('and bulk action is on and action can only be shown for single case selection', () => {
          beforeEach(() => {
            $provide.constant('allowCaseLocks', false);
            compileDirective({
              isBulkMode: true
            });

            action = _.find(CaseActionsData.get(), (action) => {
              return action.action !== 'LockCases';
            });

            action.number = 1;
          });

          it('hides the action', () => {
            expect(element.isolateScope().isActionAllowed(action)).toBe(false);
          });
        });

        describe('and bulk action is on and action can be shown for multiple case selection', () => {
          beforeEach(() => {
            $provide.constant('allowCaseLocks', false);
            compileDirective({
              isBulkMode: true
            });

            action = _.find(CaseActionsData.get(), (action) => {
              return action.action !== 'LockCases';
            });

            action.number = 2;
          });

          it('shows the action', () => {
            expect(element.isolateScope().isActionAllowed(action)).toBe(true);
          });
        });

        describe('and bulk action is off and action can only be shown for single case selection', () => {
          beforeEach(() => {
            $provide.constant('allowCaseLocks', false);
            compileDirective({
              isBulkMode: false
            });

            action = _.find(CaseActionsData.get(), (action) => {
              return action.action !== 'LockCases';
            });

            action.number = 1;
          });

          it('shows the action', () => {
            expect(element.isolateScope().isActionAllowed(action)).toBe(true);
          });
        });

        describe('and bulk action is off and action can be shown for multiple case selection', () => {
          beforeEach(() => {
            $provide.constant('allowCaseLocks', false);
            compileDirective({
              isBulkMode: false
            });

            action = _.find(CaseActionsData.get(), (action) => {
              return action.action !== 'LockCases';
            });

            action.number = 2;
          });

          it('hides the action', () => {
            expect(element.isolateScope().isActionAllowed(action)).toBe(false);
          });
        });
      });
    });
    // TODO: FINISH REST of the unit test

    /**
     * Compiles the directive
     *
     * @param {object} options options
     */
    function compileDirective (options) {
      options = options || {};

      var isBulkMode = options.isBulkMode ? 'is-bulk-mode="true"' : '';
      var markup = `
        <div
          civicase-case-actions=[]
          ${isBulkMode}
        ></div>
      `;

      element = $compile(markup)($rootScope);
      $rootScope.$digest();
    }
  });
})(CRM._);
