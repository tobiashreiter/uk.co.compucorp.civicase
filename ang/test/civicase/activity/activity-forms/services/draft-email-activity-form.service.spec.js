/* eslint-env jasmine */
((_, getCrmUrl) => {
  describe('DraftEmailActivityForm', () => {
    let activity, activityFormUrl, checkIfDraftActivity,
      DraftEmailActivityForm, expectedActivityFormUrl;

    beforeEach(module('civicase', 'civicase-base', 'civicase.data', ($provide) => {
      checkIfDraftActivity = jasmine.createSpy('checkIfDraftActivity');

      $provide.value('checkIfDraftActivity', checkIfDraftActivity);
    }));

    beforeEach(inject((_activitiesMockData_, _DraftEmailActivityForm_) => {
      DraftEmailActivityForm = _DraftEmailActivityForm_;
      activity = _.chain(_activitiesMockData_.get())
        .first()
        .cloneDeep()
        .value();
    }));

    describe('handling activity forms', () => {
      let canHandleResult, mockCheckIfDraftActivityResult;

      beforeEach(() => {
        mockCheckIfDraftActivityResult = _.uniqueId();
        checkIfDraftActivity.and.returnValue(mockCheckIfDraftActivityResult);

        canHandleResult = DraftEmailActivityForm.canHandleActivity(activity);
      });

      it('uses the check draft activity to determine if it can handle the given activity', () => {
        expect(checkIfDraftActivity).toHaveBeenCalledWith(activity, ['Email']);
      });

      it('returns the result from check draft activity directly', () => {
        expect(canHandleResult).toBe(mockCheckIfDraftActivityResult);
      });
    });

    describe('when getting the form url', () => {
      beforeEach(() => {
        activityFormUrl = DraftEmailActivityForm.getActivityFormUrl(activity);
        expectedActivityFormUrl = getCrmUrl('civicrm/activity/email/add', {
          action: 'add',
          caseId: activity.case_id,
          context: 'standalone',
          draft_id: activity.id,
          reset: '1'
        });
      });

      it('returns the popup form url for the draft activity', () => {
        expect(activityFormUrl).toEqual(expectedActivityFormUrl);
      });
    });
  });
})(CRM._, CRM.url);
