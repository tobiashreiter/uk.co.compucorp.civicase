/* eslint-env jasmine */

((_) => {
  describe('Contact Case Tab', () => {
    var $controller, $rootScope, $scope, mockContactId, mockContactService;

    beforeEach(module('civicase', ($provide) => {
      mockContactService = jasmine.createSpyObj('Contact', ['getContactIDFromUrl']);

      $provide.value('Contact', mockContactService);
    }));

    beforeEach(inject((_$controller_, _$rootScope_) => {
      $controller = _$controller_;
      $rootScope = _$rootScope_;
    }));

    beforeEach(() => {
      mockContactId = _.uniqueId();

      mockContactService.getContactIDFromUrl.and.returnValue(mockContactId);
      initController();
    });

    describe('on init', () => {
      it('stores the contact id extracted from the URL', () => {
        expect($scope.contactId).toBe(mockContactId);
      });
    });

    /**
     * Initializes the contact case tab controller.
     */
    function initController () {
      $scope = $rootScope.$new();

      $controller('CivicaseContactCaseTabController', { $scope: $scope });
    }
  });
})(CRM._);
