/* eslint-env jasmine */

(function (_, $) {
  describe('WebformsCaseAction', function () {
    var WebformsCaseAction, attributes;

    beforeEach(module('civicase'));

    beforeEach(inject(function (_WebformsCaseAction_) {
      WebformsCaseAction = _WebformsCaseAction_;
    }));

    describe('isActionAllowed()', function () {
      beforeEach(function () {
        attributes = {};
      });

      describe('when attribute.mode is set to case-details', function () {
        beforeEach(function () {
          attributes.mode = 'case-details';
        });

        it('should return true', function () {
          expect(WebformsCaseAction.isActionAllowed(null, null, attributes)).toBeTrue();
        });
      });

      describe('when attribute.mode is set to case-bulk-actions', function () {
        beforeEach(function () {
          attributes.mode = 'case-bulk-actions';
        });

        it('should return false', function () {
          expect(WebformsCaseAction.isActionAllowed(null, null, attributes)).toBeFalse();
        });
      });

      describe('when attribute.mode is undefined', function () {
        it('should return false', function () {
          expect(WebformsCaseAction.isActionAllowed(null, null, attributes)).toBeFalse();
        });
      });
    });
  });
})(CRM._, CRM.$);
