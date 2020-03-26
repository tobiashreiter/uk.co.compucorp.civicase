(function (angular, $, _, CRM) {
  var module = angular.module('civicase-base');

  module.service('TagsHelper', TagsHelper);

  /**
   * Tags Helper Service
   */
  function TagsHelper () {
    this.formatTags = formatTags;
    this.prepareGenericTags = prepareGenericTags;
    this.prepareTagSetsTree = prepareTagSetsTree;

    /**
     * Format Tags to add indentation
     *
     * @param {object} item tag object
     * @returns {string} markup for the tags
     */
    function formatTags (item) {
      return '<span style="margin-left:' + (item.indentationLevel * 4) + 'px">' + item.text + '</span>';
    }

    /**
     * Recursive function to prepare the generic tags
     *
     * @param {Array} tags tags
     * @param {string} parentID id of the parent tag
     * @param {number} level level of tag
     * @returns {Array} tags list
     */
    function prepareGenericTags (tags, parentID, level) {
      var returnArray = [];

      level = typeof level !== 'undefined' ? level : 0;
      parentID = typeof parent !== 'undefined' ? parentID : undefined;

      var filteredTags = _.filter(tags, function (child) {
        return child.parent_id === parentID && child.is_tagset === '0';
      });

      if (_.isEmpty(filteredTags)) {
        return [];
      }

      _.each(filteredTags, function (tag) {
        tag.text = tag.name;
        returnArray.push(tag);
        tag.indentationLevel = level;
        returnArray = returnArray.concat(prepareGenericTags(tags, tag.id, level + 1));
      });

      return returnArray;
    }

    /**
     * Prepares the tag sets tree
     *
     * @param {Array} tags list of tags
     * @returns {Array} tags tree
     */
    function prepareTagSetsTree (tags) {
      var returnArray = [];

      var filteredTags = _.filter(tags, function (child) {
        return !child.parent_id && child.is_tagset === '1';
      });

      if (_.isEmpty(filteredTags)) {
        return [];
      }

      _.each(filteredTags, function (tag) {
        var children = _.filter(tags, function (child) {
          if (child.parent_id === tag.id && child.is_tagset === '0') {
            child.text = child.name;
            return true;
          }
        });

        if (children.length > 0) {
          tag.children = children;
        }

        returnArray.push(tag);
      });

      return returnArray;
    }
  }
})(angular, CRM.$, CRM._, CRM);
