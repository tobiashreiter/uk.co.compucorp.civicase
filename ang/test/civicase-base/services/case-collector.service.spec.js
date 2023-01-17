(() => {
  describe('Case Collector', () => {
    let CaseCollector, CaseCollectorData;

    beforeEach(module('civicase', 'civicase.data'));

    beforeEach(inject((_CaseCollector_, _CaseCollectors_) => {
      CaseCollector = _CaseCollector_;
      CaseCollectorData = _CaseCollectors_.values;
    }));

    describe('when getting all case collectors', () => {
      let returnedCaseCollectors;

      beforeEach(() => {
        returnedCaseCollectors = CaseCollector.getAll();
      });

      it('returns all the case collectors', () => {
        expect(returnedCaseCollectors).toEqual(CaseCollectorData);
      });
    });

    describe('when getting the labels for case collectors using collector values', () => {
      let returnedLabels;

      beforeEach(() => {
        returnedLabels = CaseCollector.getLabelsForValues(['2', '3']);
      });

      it('returns the labels for the given collector values', () => {
        expect(returnedLabels).toEqual(['Resolved', 'Urgent']);
      });
    });
  });
})();
