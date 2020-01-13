/* eslint-env jasmine */
((_) => {
  describe('checkIfDraftEmailOrPDFActivity', () => {
    let activity, checkIfDraftEmailOrPDFActivity, emailActivityTypeId,
      isDraftEmailOrPdf, pdfActivityTypeId;

    beforeEach(module('civicase.data', 'civicase'));

    beforeEach(inject((_activitiesMockData_, _ActivityTypesData_,
      _checkIfDraftEmailOrPDFActivity_) => {
      checkIfDraftEmailOrPDFActivity = _checkIfDraftEmailOrPDFActivity_;
      emailActivityTypeId = _.chain(_ActivityTypesData_.values)
        .findKey({ name: 'Email' })
        .cloneDeep()
        .value();
      pdfActivityTypeId = _.chain(_ActivityTypesData_.values)
        .findKey({ name: 'Print PDF Letter' })
        .cloneDeep()
        .value();
      activity = _.chain(_activitiesMockData_.get())
        .first()
        .cloneDeep()
        .value();
    }));

    describe('when checking an email draft', () => {
      beforeEach(() => {
        activity.activity_type_id = emailActivityTypeId;
        activity.status_name = 'Draft';
        isDraftEmailOrPdf = checkIfDraftEmailOrPDFActivity(activity);
      });

      it('returns true', () => {
        expect(isDraftEmailOrPdf).toBe(true);
      });
    });

    describe('when checking a non draft email', () => {
      beforeEach(() => {
        activity.activity_type_id = emailActivityTypeId;
        activity.status_name = 'Completed';
        isDraftEmailOrPdf = checkIfDraftEmailOrPDFActivity(activity);
      });

      it('returns false', () => {
        expect(isDraftEmailOrPdf).toBe(false);
      });
    });

    describe('when checking a PDF draft', () => {
      beforeEach(() => {
        activity.activity_type_id = pdfActivityTypeId;
        activity.status_name = 'Draft';
        isDraftEmailOrPdf = checkIfDraftEmailOrPDFActivity(activity);
      });

      it('returns true', () => {
        expect(isDraftEmailOrPdf).toBe(true);
      });
    });

    describe('when checking a non draft PDF', () => {
      beforeEach(() => {
        activity.activity_type_id = pdfActivityTypeId;
        activity.status_name = 'Completed';
        isDraftEmailOrPdf = checkIfDraftEmailOrPDFActivity(activity);
      });

      it('returns false', () => {
        expect(isDraftEmailOrPdf).toBe(false);
      });
    });
  });
})(CRM._);
