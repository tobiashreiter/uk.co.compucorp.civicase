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
      getTags()
        .then(function (tags) {
          var model = setModelObjectForModal(tags);

          openTagsModal(model, cases, action.title);
        });
    };

    /**
     * Opens the modal for addition of tags
     *
     * @param {object} model model object for dialog box
     * @param {Array} cases list of cases
     * @param {string} title title of the dialog box
     */
    function openTagsModal (model, cases, title) {
      dialogService.open('EditTags', '~/civicase/case/actions/directives/edit-tags.html', model, {
        autoOpen: false,
        height: 'auto',
        width: '450px',
        title: title,
        buttons: [{
          text: 'Save',
          icons: { primary: 'fa-check' },
          click: function () {
            editTagModalClickEvent.call(this, model, cases);
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
     * @param {Array} cases list of cases
     */
    function editTagModalClickEvent (model, cases) {
      civicaseCrmApi('EntityTag', 'createByQuery', {
        entity_table: 'civicrm_case',
        tag_id: model.selectedTags,
        entity_id: cases[0].id
      });

      $(this).dialog('close');
    }
  }
})(angular, CRM.$, CRM._);
