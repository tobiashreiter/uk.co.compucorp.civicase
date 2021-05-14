(function ($, _) {
  describe('civicaseActivityFilters', () => {
    var $compile, $rootScope, $scope, activityFilters, CaseTypeCategory,
      categoryWhereUserCanAccessActivities, ActivityType, ActivityStatus;

    beforeEach(module('civicase.data', 'civicase', 'civicase.templates', () => {
      killDirective('civicaseActivityFiltersContact');
    }));

    beforeEach(inject((_$compile_, _$rootScope_, _CaseTypeCategory_,
      _ActivityType_, _ActivityStatus_) => {
      $compile = _$compile_;
      $rootScope = _$rootScope_;
      CaseTypeCategory = _CaseTypeCategory_;
      ActivityType = _ActivityType_;
      ActivityStatus = _ActivityStatus_;

      categoryWhereUserCanAccessActivities = _.sample(CaseTypeCategory.getAll(), 1);
      spyOn($rootScope, '$broadcast').and.callThrough();
      spyOn(CaseTypeCategory, 'getCategoriesWithAccessToActivity')
        .and.returnValue([categoryWhereUserCanAccessActivities]);

      $scope = $rootScope.$new();
      $scope.filters = {};

      initDirective();
    }));

    describe('on init', () => {
      it('displays a list of case type categories for which the user has permission to see the activities', () => {
        expect(activityFilters.isolateScope().caseTypeCategories)
          .toEqual([categoryWhereUserCanAccessActivities]);
      });

      it('does not filter the activity list with case type category', () => {
        expect(activityFilters.isolateScope().filters.case_type_category).toBeUndefined();
      });

      describe('when user can select case type category filter', () => {
        beforeEach(() => {
          $scope.canSelectCaseTypeCategory = true;
          initDirective();
        });

        it('filters the activity list with the first available case type category', () => {
          expect(activityFilters.isolateScope().filters.case_type_category)
            .toEqual(categoryWhereUserCanAccessActivities.name);
        });
      });
    });

    describe('when clicking more filters button', () => {
      beforeEach(() => {
        activityFilters.isolateScope().filters['@moreFilters'] = true;
        activityFilters.isolateScope().toggleMoreFilters();
      });

      it('toggles more filters visibility', () => {
        expect(activityFilters.isolateScope().filters['@moreFilters']).toEqual(false);
      });

      it('fires an event', () => {
        expect($rootScope.$broadcast)
          .toHaveBeenCalledWith('civicase::activity-filters::more-filters-toggled');
      });
    });

    describe('Activity Type Filters', () => {
      var activityTypeFilters;

      beforeEach(() => {
        activityTypeFilters = angular.copy(
          _.find(activityFilters.isolateScope().availableFilters, { name: 'activity_type_id' })
        );
      });

      it('shows the activity type filters', () => {
        expect(activityTypeFilters).toEqual({
          name: 'activity_type_id',
          label: 'Activity type',
          html_type: 'Select',
          options: _.map(ActivityType.getAll(), mapSelectOptions)
        });
      });
    });

    describe('Activity Status Filters', () => {
      var activityStatusFilters;

      beforeEach(() => {
        activityStatusFilters = angular.copy(
          _.find(activityFilters.isolateScope().availableFilters, { name: 'status_id' })
        );
      });

      it('shows the activity status filters', () => {
        expect(activityStatusFilters).toEqual({
          name: 'status_id',
          label: 'Status',
          html_type: 'Select',
          options: _.map(ActivityStatus.getAll(), mapSelectOptions)
        });
      });
    });

    describe('Target Contact Filters', () => {
      var targetContactFilters;

      beforeEach(() => {
        targetContactFilters = angular.copy(
          _.find(activityFilters.isolateScope().availableFilters, { name: 'target_contact_id' })
        );
      });

      it('shows the target contact filters', () => {
        expect(targetContactFilters).toEqual({
          name: 'target_contact_id',
          label: ts('With'),
          html_type: 'Autocomplete-Select',
          entity: 'Contact'
        });
      });
    });

    describe('Assignee Contact Filters', () => {
      var assigneeContactFilters;

      beforeEach(() => {
        assigneeContactFilters = angular.copy(
          _.find(activityFilters.isolateScope().availableFilters, { name: 'assignee_contact_id' })
        );
      });

      it('shows the Assignee contact filters', () => {
        expect(assigneeContactFilters).toEqual({
          name: 'assignee_contact_id',
          label: ts('Assigned to'),
          html_type: 'Autocomplete-Select',
          entity: 'Contact'
        });
      });
    });

    describe('Tags Filters', () => {
      var tagsFilters;

      beforeEach(() => {
        tagsFilters = angular.copy(
          _.find(activityFilters.isolateScope().availableFilters, { name: 'tag_id' })
        );
      });

      it('shows the Tags filters', () => {
        expect(tagsFilters).toEqual({
          name: 'tag_id',
          label: ts('Tagged'),
          html_type: 'Autocomplete-Select',
          entity: 'Tag',
          api_params: { used_for: { LIKE: '%civicrm_activity%' }, is_tagset: 0 }
        });
      });
    });

    describe('Text Filters', () => {
      var textFilters;

      beforeEach(() => {
        textFilters = angular.copy(
          _.find(activityFilters.isolateScope().availableFilters, { name: 'text' })
        );
      });

      it('shows the text filters', () => {
        expect(textFilters).toEqual({
          name: 'text',
          label: ts('Contains text'),
          html_type: 'Text'
        });
      });
    });

    describe('Date Filters', () => {
      var dateFilters;

      beforeEach(() => {
        dateFilters = angular.copy(
          _.find(activityFilters.isolateScope().availableFilters, { name: 'activity_date_time' })
        );
      });

      it('shows the text filters', () => {
        expect(dateFilters).toEqual({
          name: 'activity_date_time',
          label: ts('Activity date'),
          html_type: 'Select Date'
        });
      });
    });

    /**
     * Initializes the ActivityPanel directive
     */
    function initDirective () {
      activityFilters = $compile(`<div
          civicase-activity-filters="filters"
          can-select-case-type-category="canSelectCaseTypeCategory"
        ></div>`)($scope);
      $rootScope.$digest();
    }

    /**
     * Mocks a directive
     *
     * @param {string} directiveName name of the directive
     */
    function killDirective (directiveName) {
      angular.mock.module(($compileProvider) => {
        $compileProvider.directive(directiveName, () => {
          return {
            priority: 9999999,
            terminal: true
          };
        });
      });
    }

    /**
     * Maps Options to be used in the dropdown
     *
     * @param {object} option option
     * @returns {object} options
     */
    function mapSelectOptions (option) {
      return {
        id: option.value,
        text: option.label,
        color: option.color,
        icon: option.icon
      };
    }
  });
})(CRM.$, CRM._);
