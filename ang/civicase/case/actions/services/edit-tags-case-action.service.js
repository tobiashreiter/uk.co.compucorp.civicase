(function (angular, $, _) {
  var module = angular.module('civicase');

  module.service('EditTagsCaseAction', EditTagsCaseAction);

  /**
   *
   * @param {object} dialogService dialog service
   * @param {object} civicaseCrmApi service to use civicrm api
   */
  function EditTagsCaseAction (dialogService, civicaseCrmApi) {
    /**
     * Click event handler for the Action
     *
     * @param {Array} cases cases
     * @param {object} action action
     * @param {Function} callbackFn callback function
     */
    this.doAction = function (cases, action, callbackFn) {
      var existingTags = _.values(cases[0].tag_id).map(function (tag) {
        return tag.tag_id;
      });

      var casesObj = {
        caseID: cases[0].id,
        existingTags: existingTags,
        callbackFn: callbackFn
      };

      getTags()
        .then(function (tags) {
          var model = setModelObjectForModal(tags);

          model.selectedTags = existingTags;

          openTagsModal(model, action.title, casesObj);
        });
    };

    /**
     * Opens the modal for addition of tags
     *
     * @param {object} model model object for dialog box
     * @param {string} title title of the dialog box
     * @param {object} casesObj cases object
     */
    function openTagsModal (model, title, casesObj) {
      dialogService.open('EditTags', '~/civicase/case/actions/directives/edit-tags.html', model, {
        autoOpen: false,
        height: 'auto',
        width: '450px',
        title: title,
        buttons: [{
          text: 'Save',
          icons: { primary: 'fa-check' },
          click: function () {
            editTagModalClickEvent.call(this, model, casesObj);
          }
        }]
      });
    }

    /**
     * Set the model object to be used in the modal
     *
     * @param {Array} tags tags
     * @returns {object} model object for the dialog box
     */
    function setModelObjectForModal (tags) {
      var model = {};

      model.allTags = tags;
      model.selectedTags = [];

      return model;
    }

    /**
     * Get the tags for Cases from API end point
     *
     * @returns {Promise} api call promise
     */
    function getTags () {
      return civicaseCrmApi('Tag', 'get', {
        sequential: 1,
        used_for: { LIKE: '%civicrm_case%' },
        options: { limit: 0 }
      }).then(function (data) {
        return data.values;
      });
    }

    /**
     * Handles the click event for the Edit Tag Modal's Click Event
     *
     * @param {object} model model object of the modal
     * @param {object} casesObj cases object
     */
    function editTagModalClickEvent (model, casesObj) {
      var calls = [];

      var tagsToRemove = _.difference(casesObj.existingTags, model.selectedTags);
      var tagsToAdd = _.difference(model.selectedTags, casesObj.existingTags);

      if (tagsToRemove.length) {
        calls.push(['EntityTag', 'deleteByQuery', {
          entity_id: casesObj.caseID,
          tag_id: tagsToRemove,
          entity_table: 'civicrm_case'
        }]);
      }

      if (tagsToAdd.length) {
        calls.push(['EntityTag', 'createByQuery', {
          entity_id: casesObj.caseID,
          tag_id: tagsToAdd,
          entity_table: 'civicrm_case'
        }]);
      }

      if (calls.length) {
        calls.push(['Activity', 'create', {
          case_id: casesObj.caseID,
          status_id: 'Completed',
          activity_type_id: 'Change Case Tags'
        }]);

        casesObj.callbackFn(calls);
      }

      $(this).dialog('close');
    }
  }
})(angular, CRM.$, CRM._);
