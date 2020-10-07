/* eslint-env jasmine */

((_) => {
  describe('crm service', () => {
    let $q, $rootScope, crmApiMock, civicaseCrmApi;

    beforeEach(module('civicase-base', ($provide) => {
      crmApiMock = jasmine.createSpy('crmApi');

      $provide.value('crmApi', crmApiMock);
    }));

    beforeEach(inject((_$q_, _$rootScope_, _civicaseCrmApi_) => {
      $q = _$q_;
      $rootScope = _$rootScope_;
      civicaseCrmApi = _civicaseCrmApi_;
    }));

    describe('when calling the api with an array of requests', () => {
      describe('and when one of the requests returns an error', () => {
        var isErrorThrown = false;

        beforeEach(() => {
          crmApiMock.and.returnValue($q.resolve([
            { is_error: false },
            { is_error: true }
          ]));

          civicaseCrmApi([
            ['SomeEntity', 'someendpoint', {}],
            ['SomeEntity2', 'someendpoint2', {}]
          ]).catch(function () {
            isErrorThrown = true;
          });
          $rootScope.$digest();
        });

        it('calls the backend with for all the requested information', () => {
          expect(crmApiMock.calls.mostRecent().args[0]).toEqual([
            ['SomeEntity', 'someendpoint', {}],
            ['SomeEntity2', 'someendpoint2', {}]
          ]);
        });

        it('throws an exception', () => {
          expect(isErrorThrown).toBe(true);
        });
      });

      describe('and none one of the requests returns an error', () => {
        var isErrorThrown = false;

        beforeEach(() => {
          crmApiMock.and.returnValue($q.resolve([
            { is_error: false },
            { is_error: false }
          ]));

          civicaseCrmApi([
            ['SomeEntity', 'someendpoint', {}],
            ['SomeEntity2', 'someendpoint2', {}]
          ]).catch(function () {
            isErrorThrown = true;
          });
          $rootScope.$digest();
        });

        it('calls the backend with for all the requested information', () => {
          expect(crmApiMock.calls.mostRecent().args[0]).toEqual([
            ['SomeEntity', 'someendpoint', {}],
            ['SomeEntity2', 'someendpoint2', {}]
          ]);
        });

        it('does not throw an exception', () => {
          expect(isErrorThrown).toBe(false);
        });
      });
    });
  });
})(CRM._);
