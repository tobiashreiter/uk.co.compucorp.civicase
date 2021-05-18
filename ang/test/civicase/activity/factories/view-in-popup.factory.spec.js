(($, _) => {
  describe('viewInPopup', () => {
    let viewInPopup, mockGetActivityFormService, mockGetActivityFormUrl,
      civicaseCrmLoadForm, loadFormOnListener, crmFormSuccessFunction,
      $rootScope;

    const CRM_FORM_SUCCESS_EVENT = 'crmFormSuccess crmPopupFormSuccess';

    beforeEach(module('civicase', 'civicase.data', ($provide) => {
      mockGetActivityFormService = jasmine.createSpy('getActivityFormService');
      mockGetActivityFormUrl = jasmine.createSpy('getActivityFormUrl');
      mockGetActivityFormUrl.and.returnValue('mock GetActivityFormUrl return value');

      mockGetActivityFormService.and.returnValue({
        getActivityFormUrl: mockGetActivityFormUrl
      });

      $provide.value('ActivityForms', { getActivityFormService: mockGetActivityFormService });

      var civicaseCrmLoadFormSpy = jasmine.createSpy('loadForm');
      loadFormOnListener = jasmine.createSpyObj('', ['on']);
      loadFormOnListener.on.and.callFake(function () {
        if (arguments[0] === CRM_FORM_SUCCESS_EVENT) {
          crmFormSuccessFunction = arguments[1];
        }

        return loadFormOnListener;
      });

      civicaseCrmLoadFormSpy.and.returnValue(loadFormOnListener);
      $provide.service('civicaseCrmLoadForm', function () {
        return civicaseCrmLoadFormSpy;
      });
    }));

    beforeEach(inject((_viewInPopup_, _civicaseCrmLoadForm_, _$rootScope_) => {
      viewInPopup = _viewInPopup_;
      civicaseCrmLoadForm = _civicaseCrmLoadForm_;
      $rootScope = _$rootScope_;

      spyOn($rootScope, '$broadcast');
    }));

    describe('when clicking a button', () => {
      let activity;

      beforeEach(() => {
        const event = $.Event('click');
        event.target = document.createElement('a');

        activity = { type: 'email' };
        viewInPopup(event, activity);
      });

      it('does not show the activity in a popup', () => {
        expect(mockGetActivityFormUrl).not.toHaveBeenCalled();
      });
    });

    describe('when not clicking a button', () => {
      let activity, event;

      beforeEach(() => {
        event = $.Event('click');
        event.target = document.createElement('span');
      });

      describe('and we want to update the activity', () => {
        beforeEach(function () {
          activity = { type: 'Meeting' };
          viewInPopup(event, activity);
          crmFormSuccessFunction();
        });

        it('shows the activity in a popup in update mode', function () {
          expect(mockGetActivityFormService).toHaveBeenCalledWith(activity, { action: 'update' });
          expect(mockGetActivityFormUrl).toHaveBeenCalledWith(activity, { action: 'update' });
          expect(civicaseCrmLoadForm).toHaveBeenCalledWith('mock GetActivityFormUrl return value');
        });

        it('refreshes the activity feed', () => {
          expect($rootScope.$broadcast).toHaveBeenCalledWith('civicase::activity::updated');
        });
      });

      describe('and we want to view the activity', () => {
        beforeEach(() => {
          activity = { type: 'Meeting' };
          viewInPopup(event, activity, {
            isReadOnly: true
          });
          crmFormSuccessFunction();
        });

        it('shows the activity in a popup in view mode', () => {
          expect(mockGetActivityFormService).toHaveBeenCalledWith(activity, { action: 'view' });
          expect(mockGetActivityFormUrl).toHaveBeenCalledWith(activity, { action: 'view' });
          expect(civicaseCrmLoadForm).toHaveBeenCalledWith('mock GetActivityFormUrl return value');
        });

        it('refreshes the activity feed', () => {
          expect($rootScope.$broadcast).toHaveBeenCalledWith('civicase::activity::updated');
        });
      });
    });
  });
})(CRM.$, CRM._);
