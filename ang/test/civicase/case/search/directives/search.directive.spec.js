/* eslint-env jasmine */
(($, _) => {
  describe('civicaseSearch', () => {
    let $controller, $rootScope, $scope, CaseFilters, CaseStatuses, CaseTypes, crmApi, affixOriginalFunction,
      offsetOriginalFunction, originalDoSearch, originalParentScope, affixReturnValue,
      originalBindToRoute;

    beforeEach(module('civicase.templates', 'civicase', 'civicase.data', ($provide) => {
      crmApi = jasmine.createSpy('crmApi');

      $provide.value('crmApi', crmApi);
    }));

    beforeEach(inject((_$controller_, $q, _$rootScope_, _CaseFilters_, _CaseStatuses_, _CaseTypesMockData_) => {
      $controller = _$controller_;
      $rootScope = _$rootScope_;
      $scope = $rootScope.$new();
      CaseFilters = _CaseFilters_;
      CaseStatuses = _CaseStatuses_.values;
      CaseTypes = _CaseTypesMockData_.get();

      crmApi.and.returnValue($q.resolve({ values: [] }));
    }));

    beforeEach(() => {
      affixOriginalFunction = CRM.$.fn.affix;
      offsetOriginalFunction = CRM.$.fn.offset;

      CRM.$.fn.offset = () => ({ top: 100 });

      CRM.$.fn.affix = jasmine.createSpy('affix');
      affixReturnValue = jasmine.createSpyObj('affix', ['on']);
      affixReturnValue.on.and.returnValue(affixReturnValue);
      CRM.$.fn.affix.and.returnValue(affixReturnValue);
      originalBindToRoute = $scope.$bindToRoute;
      $scope.$bindToRoute = jasmine.createSpy('$bindToRoute');

      initController();
    });

    afterEach(() => {
      CRM.$.fn.affix = affixOriginalFunction;
      CRM.$.fn.offset = offsetOriginalFunction;
      $scope.$bindToRoute = originalBindToRoute;
    });

    describe('$scope variables', () => {
      it('checks $scope.caseTypeOptions', () => {
        expect($scope.caseTypeOptions).toEqual(jasmine.any(Object));
      });

      it('checks $scope.caseStatusOptions', () => {
        expect($scope.caseStatusOptions).toEqual(jasmine.any(Object));
      });

      it('checks $scope.customGroups', () => {
        expect($scope.customGroups).toEqual(jasmine.any(Object));
      });

      it('checks $scope.caseRelationshipOptions', () => {
        expect($scope.caseRelationshipOptions).toEqual(jasmine.any(Object));
      });

      it('checks $scope.checkPerm', () => {
        expect($scope.checkPerm).toEqual(jasmine.any(Function));
      });

      it('checks $scope.filterDescription', () => {
        expect($scope.filterDescription).toEqual(jasmine.any(Array));
      });

      it('checks $scope.filters', () => {
        expect($scope.filterDescription).toEqual(jasmine.any(Object));
      });
    });

    describe('watchers', () => {
      describe('when updating the relationship types', () => {
        describe('when I am the case manager', () => {
          beforeEach(() => {
            $scope.relationshipType = ['is_case_manager'];
            $scope.$digest();
          });

          it('sets the case manager filter equal to my id', () => {
            expect($scope.filters.case_manager).toEqual([CRM.config.user_contact_id]);
          });
        });

        describe('when I am involved in the case', () => {
          beforeEach(() => {
            $scope.relationshipType = ['is_involved'];
            $scope.$digest();
          });

          it('sets the contact id filter equal to my id', function () {
            expect($scope.filters.contact_involved).toEqual([CRM.config.user_contact_id]);
          });
        });
      });

      describe('$scope.filters', () => {
        beforeEach(() => {
          originalDoSearch = $scope.doSearch;
          $scope.doSearch = jasmine.createSpy('doSearch');
          $scope.filters = CaseFilters.filter;
        });

        afterEach(() => {
          $scope.doSearch = originalDoSearch;
        });

        describe('when $scope.expanded is false', () => {
          beforeEach(() => {
            $scope.expanded = false;
            $scope.$digest();
          });

          it('calls $scope.doSearch()', () => {
            expect($scope.doSearch).toHaveBeenCalled();
          });
        });

        describe('when $scope.expanded is true', () => {
          beforeEach(() => {
            $scope.expanded = true;
            $scope.$digest();
          });

          it('does not calls $scope.doSearch()', () => {
            expect($scope.doSearch).not.toHaveBeenCalled();
          });
        });
      });
    });

    describe('caseManagerIsMe()', () => {
      describe('when case_manager is me', () => {
        beforeEach(() => {
          $scope.filters.case_manager = [203];
        });

        it('should return true', () => {
          expect($scope.caseManagerIsMe()).toBe(true);
        });
      });

      describe('when case_manager is not me', () => {
        describe('when case id is different', () => {
          beforeEach(() => {
            $scope.filters.case_manager = [201];
          });

          it('should return false', () => {
            expect($scope.caseManagerIsMe()).toBe(false);
          });
        });

        describe('when case id undefined', () => {
          beforeEach(() => {
            $scope.filters.case_manager = undefined;
          });

          it('should return undefined', () => {
            expect($scope.caseManagerIsMe()).toBeUndefined();
          });
        });
      });
    });

    describe('accepting URL values for the relationship type filter', () => {
      describe('when setting the case manager as the logged in user', () => {
        beforeEach(() => {
          $scope.$bindToRoute.and.callFake(() => {
            $scope.filters = {
              case_manager: 'user_contact_id'
            };
          });
          initController();
          $scope.$digest();
        });

        it('sets the relationship type filter equal to "My Cases"', () => {
          expect($scope.relationshipType).toEqual(['is_case_manager']);
        });

        it('sets the case manager filter equal to the current logged in user id', () => {
          expect($scope.filters.case_manager).toEqual([CRM.config.user_contact_id]);
        });
      });

      describe('when setting the contact involved as the logged in user', () => {
        beforeEach(() => {
          $scope.$bindToRoute.and.callFake(() => {
            $scope.filters = {
              contact_involved: 'user_contact_id'
            };
          });
          initController();
          $scope.$digest();
        });

        it('sets the relationship type filter equal to "Cases I am involved"', () => {
          expect($scope.relationshipType).toEqual(['is_involved']);
        });

        it('sets the contact involved filter equal to the current logged in user id', () => {
          expect($scope.filters.contact_involved).toEqual([CRM.config.user_contact_id]);
        });
      });
    });

    describe('doSearch()', () => {
      beforeEach(() => {
        originalParentScope = $scope.$parent;
        $scope.$parent = {};
      });

      beforeEach(() => {
        $scope.expanded = true;
        $scope.filters.case_manager = [203];
        $scope.doSearch();
      });

      afterEach(() => {
        $scope.$parent = originalParentScope;
      });

      it('should build filter description', () => {
        expect($scope.filterDescription).toEqual([{ label: 'Case Manager', text: 'Me' }]);
      });

      it('should close the dropdown', () => {
        expect($scope.expanded).toBe(false);
      });
    });

    describe('clearSearch()', () => {
      beforeEach(() => {
        originalDoSearch = $scope.doSearch;
        $scope.doSearch = jasmine.createSpy('doSearch');
        $scope.filters = CaseFilters.filter;
        $scope.clearSearch();
      });

      afterEach(() => {
        $scope.doSearch = originalDoSearch;
      });

      it('clears filters object', () => {
        expect($scope.filters).toEqual({});
      });

      it('calls doSearch()', () => {
        expect($scope.doSearch).toHaveBeenCalled();
      });
    });

    describe('mapSelectOptions()', () => {
      it('returns a mapped response', () => {
        expect($scope.caseTypeOptions[0]).toEqual(jasmine.objectContaining({ id: jasmine.any(String), text: jasmine.any(String), color: jasmine.any(String), icon: jasmine.any(String) }));
      });
    });

    describe('updating the search title', () => {
      const updateTitleEventName = 'civicase::case-search::page-title-updated';

      describe('when a case has been selected to be displayed', () => {
        const caseName = 'Housing Support';

        beforeEach(() => {
          $scope.$emit(updateTitleEventName, caseName);
        });

        it('sets the title equal to the provided case name', () => {
          expect($scope.pageTitle).toEqual(caseName);
        });
      });

      describe('when no cases have been selected', () => {
        describe('when no filters have been applied', () => {
          beforeEach(() => {
            $scope.filters = {};

            $scope.$emit(updateTitleEventName);
          });

          it('displays an "all open cases" title', () => {
            expect($scope.pageTitle).toEqual('All Open  Cases');
          });
        });

        describe('when cases are filtered by case type category', () => {
          beforeEach(() => {
            $scope.filters = {
              case_type_category: '1'
            };

            $scope.$emit(updateTitleEventName);
          });

          it('displays an "all open cases" title', () => {
            expect($scope.pageTitle).toEqual('All Open  Cases');
          });
        });

        describe('when there are filters not used for describing the title', () => {
          beforeEach(() => {
            $scope.filters = [{ case_manager: [1] }];

            $scope.$emit(updateTitleEventName);
          });

          it('displays a "catch all" title for all extra filters', () => {
            expect($scope.pageTitle).toEqual('Case Search Results');
          });
        });

        describe('when the filters can be used to describe the title', () => {
          let expectedTitle;

          describe('when filtering only by case statuses', () => {
            beforeEach(() => {
              expectedTitle = `${CaseStatuses['1'].label} & ${CaseStatuses['2'].label}  Cases`;
              $scope.filters = {
                status_id: [
                  CaseStatuses['1'].value,
                  CaseStatuses['2'].value
                ]
              };

              $scope.$emit(updateTitleEventName);
            });

            it('displays title for the statuses', () => {
              expect($scope.pageTitle).toEqual(expectedTitle);
            });
          });

          describe('when filtering only by case types', () => {
            beforeEach(() => {
              expectedTitle = `All Open ${CaseTypes['1'].title} & ${CaseTypes['2'].title} Cases`;
              $scope.filters = {
                case_type_id: [
                  CaseTypes['1'].name,
                  CaseTypes['2'].name
                ]
              };

              $scope.$emit(updateTitleEventName);
            });

            it('displays a title for the case types', () => {
              expect($scope.pageTitle).toEqual(expectedTitle);
            });
          });

          describe('when filtering by both case statuses and case types', () => {
            beforeEach(() => {
              expectedTitle = `${CaseStatuses['1'].label} & ${CaseStatuses['2'].label}` +
                ` ${CaseTypes['1'].title} & ${CaseTypes['2'].title} Cases`;
              $scope.filters = {
                status_id: [
                  CaseStatuses['1'].value,
                  CaseStatuses['2'].value
                ],
                case_type_id: [
                  CaseTypes['1'].name,
                  CaseTypes['2'].name
                ]
              };

              $scope.$emit(updateTitleEventName);
            });

            it('displays a title for the case statuses and case types', () => {
              expect($scope.pageTitle).toEqual(expectedTitle);
            });
          });
        });

        describe('when a case count is provided', () => {
          const randomCount = _.random(0, 1000);

          beforeEach(() => {
            $scope.$emit(updateTitleEventName, null, randomCount);
          });

          it('adds the count at the end of the title', () => {
            expect($scope.pageTitle).toEqual(`All Open  Cases (${randomCount})`);
          });
        });
      });
    });

    /**
     * Initiate controller
     */
    function initController () {
      $scope.filters = {};
      $scope.searchIsOpentrue = true;
      $scope.applyAdvSearch = function () { };
      $controller('civicaseSearchController', {
        $scope: $scope
      });
    }
  });
})(CRM.$, CRM._);
