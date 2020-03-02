/* eslint-env jasmine */

((_, CRM) => {
  describe('CaseTypeCategoryTranslationService', () => {
    const civicaseTranslationDomainName = 'strings::uk.co.compucorp.civicase';
    const mockCaseTypeCategoryId = _.uniqueId();
    const mockTranslation = {
      civicase: 'my custom case name'
    };
    let $rootScope, CaseTypeCategoryTranslationService;

    beforeEach(module('civicase-base'));

    beforeEach(inject((_$rootScope_, _CaseTypeCategoryTranslationService_) => {
      $rootScope = _$rootScope_;
      CaseTypeCategoryTranslationService = _CaseTypeCategoryTranslationService_;

      CRM[civicaseTranslationDomainName] = mockTranslation;
    }));

    describe('when it initializes', () => {
      it('creates a store for translations in the root scope object', () => {
        expect($rootScope.caseTypeCategoryTranslations).toEqual({});
      });
    });

    describe('when storing a case type category translation', () => {
      beforeEach(() => {
        CaseTypeCategoryTranslationService.storeTranslation(mockCaseTypeCategoryId);
      });

      it('stores the translation in the root scope object', () => {
        expect($rootScope.caseTypeCategoryTranslations[mockCaseTypeCategoryId])
          .toEqual(mockTranslation);
      });

      it('stores a copy of the translation and not the original one', () => {
        expect($rootScope.caseTypeCategoryTranslations[mockCaseTypeCategoryId])
          .not.toBe(mockTranslation);
      });
    });

    describe('when restoring a case type category translation', () => {
      beforeEach(() => {
        CaseTypeCategoryTranslationService.storeTranslation(mockCaseTypeCategoryId);
        CRM[civicaseTranslationDomainName] = {};
        CaseTypeCategoryTranslationService.restoreTranslation(mockCaseTypeCategoryId);
      });

      it('replaces the civicase translation object with the one that was previously stored in the root scope', () => {
        expect(CRM[civicaseTranslationDomainName]).toEqual(mockTranslation);
      });
    });
  });
})(CRM._, CRM);
