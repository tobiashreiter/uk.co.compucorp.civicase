/* eslint-env jasmine */

(function ($) {
  describe('ActivityCard', function () {
    var $compile, $rootScope, $scope, viewInPopup, activityCard,
      activitiesMockData, viewInPopupMockReturn, crmFormSuccessCallback;

    beforeEach(module('civicase', 'civicase.templates', 'civicase.data', function ($provide) {
      var viewInPopupMock = jasmine.createSpy('viewInPopupMock');
      viewInPopupMockReturn = jasmine.createSpyObj('viewInPopupMockObj', ['on']);
      viewInPopupMockReturn.on.and.callFake(function (event, fn) {
        crmFormSuccessCallback = fn;
      });
      viewInPopupMock.and.returnValue(viewInPopupMockReturn);

      $provide.value('viewInPopup', viewInPopupMock);
    }));

    beforeEach(inject(function (_$compile_, _$rootScope_, _activitiesMockData_, _viewInPopup_) {
      $compile = _$compile_;
      $rootScope = _$rootScope_;
      activitiesMockData = _activitiesMockData_;
      viewInPopup = _viewInPopup_;

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
      $scope = $rootScope.$new();
      $scope.activity = {};
      $scope.refreshCallback = jasmine.createSpy('refreshCallback');

      activityCard = $compile('<div case-activity-card="activity" refresh-callback="refreshCallback"></div>')($scope);
      $rootScope.$digest();
    }
  });
})(CRM.$);
