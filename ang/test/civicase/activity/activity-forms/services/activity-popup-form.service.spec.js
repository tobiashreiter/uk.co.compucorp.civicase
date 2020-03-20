/* eslint-env jasmine */
((_, getCrmUrl) => {
  describe('ActivityPopupForm', () => {
    let activity, activityFormUrl, ActivityPopupForm, expectedActivityFormUrl, canHandle;

    beforeEach(module('civicase', 'civicase-base', 'civicase.data'));

    beforeEach(inject((_activitiesMockData_, _ActivityPopupForm_) => {
      ActivityPopupForm = _ActivityPopupForm_;
      activity = _.chain(_activitiesMockData_.get())
        .first()
        .cloneDeep()
        .value();
    }));

    describe('allowing activity status change', () => {
      it('allows for activity status change', () => {
        expect(ActivityPopupForm.canChangeStatus).toBe(true);
      });
    });

    describe('handling activity forms', () => {
      describe('when handling a popup form', () => {
        beforeEach(() => {
          canHandle = ActivityPopupForm.canHandleActivity(activity, {
            formType: 'popup'
          });
        });

        it('can handle the popup form', () => {
          expect(canHandle).toBe(true);
        });
      });

      describe('when handling any other form type', () => {
        beforeEach(() => {
          canHandle = ActivityPopupForm.canHandleActivity(activity);
        });

        it('cannot handle the form', () => {
          expect(canHandle).toBe(false);
        });
      });
    });

    describe('getting the activity form url', () => {
      describe('when getting the form url for a stand alone activity', () => {
        beforeEach(() => {
          delete activity.case_id;

          activityFormUrl = ActivityPopupForm.getActivityFormUrl(activity);
          expectedActivityFormUrl = getCrmUrl('civicrm/activity', {
            action: 'update',
            id: activity.id,
            reset: 1
          });
        });

        it('returns the popup form url for the stand alone activity', () => {
          expect(activityFormUrl).toEqual(expectedActivityFormUrl);
        });
      });

      describe('when getting the form url for a case activity', () => {
        beforeEach(() => {
          activity.case_id = _.uniqueId();
          activityFormUrl = ActivityPopupForm.getActivityFormUrl(activity);
          expectedActivityFormUrl = getCrmUrl('civicrm/case/activity', {
            action: 'update',
            id: activity.id,
            reset: 1,
            caseid: activity.case_id
          });
        });

        it('returns the popup form url for the case activity', () => {
          expect(activityFormUrl).toEqual(expectedActivityFormUrl);
        });
      });
    });
  });
})(CRM._, CRM.url);
