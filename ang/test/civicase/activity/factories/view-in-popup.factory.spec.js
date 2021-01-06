/* eslint-env jasmine */

(function (_, $) {
  describe('viewInPopup', function () {
    var viewInPopup, mockGetActivityFormService, mockGetActivityFormUrl;

    beforeEach(module('civicase', 'civicase.data', function ($provide) {
      mockGetActivityFormService = jasmine.createSpy('getActivityFormService');
      mockGetActivityFormUrl = jasmine.createSpy('getActivityFormUrl');
      mockGetActivityFormUrl.and.returnValue('mock GetActivityFormUrl return value');

      mockGetActivityFormService.and.returnValue({
        getActivityFormUrl: mockGetActivityFormUrl
      });

      $provide.value('ActivityForms', { getActivityFormService: mockGetActivityFormService });
    }));

    beforeEach(inject(function (_viewInPopup_) {
      viewInPopup = _viewInPopup_;
    }));

    describe('when clicking a button', function () {
      var activity;

      beforeEach(function () {
        var event = $.Event('click');
        event.target = document.createElement('a');

        activity = { type: 'email' };
        viewInPopup(event, activity);
      });

      it('does not show the activity in a popup', function () {
        expect(mockGetActivityFormUrl).not.toHaveBeenCalled();
      });
    });

    describe('when not clicking a button', () => {
      var loadFormBefore, activity, returnValue, event;

      beforeEach(() => {
        loadFormBefore = CRM.loadForm;
        CRM.loadForm = jasmine.createSpy();
        CRM.loadForm.and.returnValue('loadForm');

        event = $.Event('click');
        event.target = document.createElement('span');
      });

      afterEach(function () {
        CRM.loadForm = loadFormBefore;
      });

      describe('and the activity is email type', function () {
        beforeEach(function () {
          activity = { type: 'Email' };
          returnValue = viewInPopup(event, activity);
        });

        it('shows the activity in a popup in view mode', function () {
          expect(mockGetActivityFormService).toHaveBeenCalledWith(activity, { action: 'view' });
          expect(mockGetActivityFormUrl).toHaveBeenCalledWith(activity);
          expect(CRM.loadForm).toHaveBeenCalledWith('mock GetActivityFormUrl return value');
          expect(returnValue).toBe('loadForm');
        });
      });

      describe('and the activity is not email type', function () {
        beforeEach(function () {
          activity = { type: 'Meeting' };
          returnValue = viewInPopup(event, activity);
        });

        it('shows the activity in a popup in update mode', function () {
          expect(mockGetActivityFormService).toHaveBeenCalledWith(activity, { action: 'update' });
          expect(mockGetActivityFormUrl).toHaveBeenCalledWith(activity);
          expect(CRM.loadForm).toHaveBeenCalledWith('mock GetActivityFormUrl return value');
          expect(returnValue).toBe('loadForm');
        });
      });
    });
  });
})(CRM._, CRM.$);
