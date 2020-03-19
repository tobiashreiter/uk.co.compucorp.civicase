/* eslint-env jasmine */
((_, getCrmUrl) => {
  describe('DraftPdfActivityForm', () => {
    let activity, activityFormUrl, checkIfDraftActivity, DraftPdfActivityForm;

    beforeEach(module('civicase', 'civicase-base', 'civicase.data', ($provide) => {
      checkIfDraftActivity = jasmine.createSpy('checkIfDraftActivity');

      $provide.value('checkIfDraftActivity', checkIfDraftActivity);
    }));

    beforeEach(inject((_activitiesMockData_, _DraftPdfActivityForm_) => {
      DraftPdfActivityForm = _DraftPdfActivityForm_;
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

        canHandleResult = DraftPdfActivityForm.canHandleActivity(activity);
      });

      it('uses the check draft activity to determine if it can handle the given activity', () => {
        expect(checkIfDraftActivity).toHaveBeenCalledWith(activity, ['Print PDF Letter']);
      });

      it('returns the result from check draft activity directly', () => {
        expect(canHandleResult).toBe(mockCheckIfDraftActivityResult);
      });
    });

    describe('when getting the form url', () => {
      let activityFormUrlParams, expectedActivityFormUrl;

      beforeEach(() => {
        activityFormUrlParams = {
          action: 'add',
          context: 'standalone',
          draft_id: activity.id,
          reset: '1'
        };
      });

      describe('when the activity is part of a case', () => {
        beforeEach(() => {
          activity['case_id.contacts'] = [
            { contact_id: _.uniqueId() },
            { contact_id: _.uniqueId() },
            { contact_id: _.uniqueId() }
          ];
          activityFormUrlParams.cid = _.map(activity['case_id.contacts'], 'contact_id');
          expectedActivityFormUrl = getCrmUrl('civicrm/activity/pdf/add', activityFormUrlParams);
          activityFormUrl = DraftPdfActivityForm.getActivityFormUrl(activity);
        });

        it('returns the popup form url for the PDF draft activity addressed to case contacts', () => {
          expect(activityFormUrl).toEqual(expectedActivityFormUrl);
        });
      });

      describe('when the activity is not part of a case', () => {
        beforeEach(() => {
          activityFormUrlParams.cid = CRM.config.user_contact_id;
          expectedActivityFormUrl = getCrmUrl('civicrm/activity/pdf/add', activityFormUrlParams);
          activityFormUrl = DraftPdfActivityForm.getActivityFormUrl(activity);
        });

        it('returns the popup form url for the PDF draft activity addressed to the logged in contact', () => {
          expect(activityFormUrl).toEqual(expectedActivityFormUrl);
        });
      });
    });
  });
})(CRM._, CRM.url);
