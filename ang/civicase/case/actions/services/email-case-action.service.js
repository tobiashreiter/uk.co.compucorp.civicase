(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('EmailCaseAction', EmailCaseAction);

  /**
   * EmailCaseAction service.
   *
   * @param {object} $q $q service
   * @param {object} ts translation service
   * @param {Function} isTruthy service to check if value is truthy
   * @param {object} dialogService dialog service
   * @param {Function} parseUrlParameters parse url parameters
   * @param {object} $window browsers window object
   * @param {object} CaseType case type service
   * @param {object} CaseTypeCategory case type category service
   * @param {object} civicaseCrmApi service to use civicrm api
   * @param {Function} getSelect2Value service to get select 2 values
   */
  function EmailCaseAction ($q, ts, isTruthy, dialogService, parseUrlParameters,
    $window, CaseType, CaseTypeCategory, civicaseCrmApi, getSelect2Value) {
    var model = {};

    /**
     * Returns the configuration options to open up a mail popup to
     * communicate with the selected role. Displays an error message
     * when no roles have been assigned to the case.
     *
     * @param {Array} cases list of cases
     * @param {object} action action to be performed
     * @param {Function} callbackFn the callback function
     *
     * @returns {Promise} promise which resolves to the path for the popup
     */
    this.doAction = function (cases, action, callbackFn) {
      model = {
        caseRoles: [],
        selectedCaseRoles: [],
        caseIds: [],
        deferObject: $q.defer()
      };

      model.caseRoles = getCaseRoles();
      model.caseIds = cases.map(function (caseObj) {
        return caseObj.id;
      });

      openRoleSelectorPopUp();

      return model.deferObject.promise;
    };

    /**
     * @param {string|number[]} caseRoleIds list of case roles ids
     * @param {string|number[]} caseIDs list of case ids
     * @returns {Promise} promise
     */
    function getContactsForCaseIds (caseRoleIds, caseIDs) {
      return civicaseCrmApi('Relationship', 'get', {
        sequential: 1,
        case_id: { IN: caseIDs },
        relationship_type_id: { IN: caseRoleIds },
        options: { limit: 0 }
      }).then(function (relationshipsData) {
        return relationshipsData.values.map(function (relationship) {
          return relationship.contact_id_b;
        });
      });
    }
    /**
     * Get case roles to be displayed on a dropdown list
     *
     * @returns {object[]} list of case roles
     */
    function getCaseRoles () {
      var caseTypeCategoryName = parseUrlParameters($window.location.search).case_type_category;
      var caseTypeCategoryID = CaseTypeCategory.findByName(caseTypeCategoryName).value;

      return _.map(CaseType.getAllRolesByCategoryID(caseTypeCategoryID), function (caseRole) {
        return _.extend(caseRole, { text: caseRole.name });
      });
    }

    /**
     * Open a popup where user can select roles
     */
    function openRoleSelectorPopUp () {
      dialogService.open(
        'EmailCaseActionRoleSelector',
        '~/civicase/case/actions/directives/email-role-selector.html',
        model,
        {
          autoOpen: false,
          height: '300px',
          width: '40%',
          title: 'Email Case Role(s)',
          buttons: [{
            text: ts('Save'),
            icons: { primary: 'fa-check' },
            click: roleSelectorClickHandler
          }]
        }
      );
    }

    /**
     * Click handler for role selector popup sace button
     */
    function roleSelectorClickHandler () {
      getContactsForCaseIds(
        getSelect2Value(model.selectedCaseRoles),
        model.caseIds
      ).then(function (contactIDs) {
        dialogService.close('EmailCaseActionRoleSelector');
        contactIDs = _.uniq(contactIDs);

        if (contactIDs.length === 0) {
          CRM.alert(
            ts('Please add a contact for the selected role(s).'),
            ts('No contacts available for selected role(s)'),
            'error'
          );
          model.deferObject.resolve();

          return;
        }

        var popupPath = {
          path: 'civicrm/activity/email/add',
          query: {
            action: 'add',
            reset: 1,
            cid: contactIDs.join(',')
          }
        };

        if (model.caseIds.length === 1) {
          popupPath.query.caseid = model.caseIds[0];
        }

        model.deferObject.resolve(popupPath);
      });
    }
  }
})(angular, CRM.$, CRM._);
