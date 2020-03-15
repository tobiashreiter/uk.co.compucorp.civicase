/* eslint-env jasmine */

(function ($, _) {
  describe('ActivityCard', function () {
    var $compile, $filter, $rootScope, $scope, viewInPopup, activityCard,
      activitiesMockData, CaseType, CaseTypeCategory,
      viewInPopupMockReturn, crmFormSuccessCallback;

    beforeEach(module('civicase', 'civicase.templates', 'civicase.data', function ($provide) {
      var viewInPopupMock = jasmine.createSpy('viewInPopupMock');
      viewInPopupMockReturn = jasmine.createSpyObj('viewInPopupMockObj', ['on']);
      viewInPopupMockReturn.on.and.callFake(function (event, fn) {
        crmFormSuccessCallback = fn;
      });
      viewInPopupMock.and.returnValue(viewInPopupMockReturn);

      $provide.value('viewInPopup', viewInPopupMock);
    }));

    beforeEach(inject(function (_$compile_, _$filter_, _$rootScope_, _activitiesMockData_,
      _CaseType_, _CaseTypeCategory_, _viewInPopup_) {
      $compile = _$compile_;
      $filter = _$filter_;
      $rootScope = _$rootScope_;
      activitiesMockData = _activitiesMockData_;
      CaseType = _CaseType_;
      CaseTypeCategory = _CaseTypeCategory_;
      viewInPopup = _viewInPopup_;

      $scope = $rootScope.$new();
      $scope.activity = {};

      $('<div id="bootstrap-theme"></div>').appendTo('body');
      initDirective();
    }));

    afterEach(function () {
      $('#bootstrap-theme').remove();
    });

    describe('on init', function () {
      it('stores a reference to the bootstrap theme element', function () {
        expect(activityCard.isolateScope().bootstrapThemeElement.is('#bootstrap-theme')).toBe(true);
      });

      describe('when the activity does not belong to a case', () => {
        it('does not store a link to a case details page', () => {
          expect($scope.caseDetailUrl).not.toBeDefined();
        });
      });

      describe('when the activity belongs to a case', () => {
        let expectedCaseDetailsUrl;

        beforeEach(() => {
          const caseTypes = CaseType.getAll();
          const caseTypeId = _.chain(caseTypes).keys().sample().value();
          const caseType = caseTypes[caseTypeId];
          const caseTypeCategory = _.find(CaseTypeCategory.getAll(), {
            value: caseType.case_type_category
          });
          $scope.activity = _.sample(activitiesMockData.get());
          $scope.activity.case_id = _.uniqueId();
          $scope.activity.case = {
            case_id: $scope.activity.case_id,
            case_type_id: caseTypeId
          };

          expectedCaseDetailsUrl = getExpectedCaseDetailsUrl(
            $scope.activity.case_id,
            caseTypeCategory.name
          );

          initDirective();
        });

        it('stores the URL to the case details for the case associated to the activity', () => {
          expect(activityCard.isolateScope().caseDetailUrl).toEqual(expectedCaseDetailsUrl);
        });

        /**
         * @param {number} caseId the case id.
         * @param {number} caseTypeCategoryName the category the case belongs to.
         * @returns {string} the expected URL to the case details for the given case.
         */
        function getExpectedCaseDetailsUrl (caseId, caseTypeCategoryName) {
          const caseDetailUrl = 'civicrm/case/a/?' +
            $.param({ case_type_category: caseTypeCategoryName }) +
            '#/case/list';
          const angularParams = $.param({ caseId });

          return $filter('civicaseCrmUrl')(caseDetailUrl) + '?' + angularParams;
        }
      });
    });

    describe('when editing an activity in the popup', function () {
      var activity;

      beforeEach(function () {
        activity = activitiesMockData.get()[0];

        activityCard.isolateScope().viewInPopup(null, activity);
      });

      it('opens the modal to edit the activity', function () {
        expect(viewInPopup).toHaveBeenCalledWith(null, activity);
      });

      it('listenes for the the form to be saved', function () {
        expect(viewInPopupMockReturn.on).toHaveBeenCalledWith('crmFormSuccess', jasmine.any(Function));
      });

      describe('when activity is saved', function () {
        beforeEach(function () {
          crmFormSuccessCallback();
        });

        it('refreshes the data when activity is saved', function () {
          expect(activityCard.isolateScope().refresh).toHaveBeenCalled();
        });
      });
    });

    /**
     * Initializes the ActivityCard directive
     */
    function initDirective () {
      $scope.refreshCallback = jasmine.createSpy('refreshCallback');

      activityCard = $compile('<div case-activity-card="activity" refresh-callback="refreshCallback"></div>')($scope);
      $rootScope.$digest();
    }
  });
})(CRM.$, CRM._);
