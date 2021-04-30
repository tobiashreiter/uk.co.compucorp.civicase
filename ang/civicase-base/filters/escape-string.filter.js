(function (_, angular) {
  var module = angular.module('civicase-base');

  module.filter('civicaseEscapeString', civicaseEscapeString);

  /**
   * Escape String Filter.
   *
   * @returns {Function} the service reference.
   */
  function civicaseEscapeString () {
    /**
     * Escapes HTML entities for the given string.
     *
     * We unescape and then escape the string because the value might already
     * be escaped and if we try to scape it twice the string might break.
     * Ex: `&amp;` will turn into `&amp;amp;`.
     *
     * @param {string} stringToBeEscaped the string to be escaped.
     * @returns {string} escaped tring.
     */
    return function escapeString (stringToBeEscaped) {
      return _.escape(_.unescape(stringToBeEscaped));
    };
  }
})(CRM._, angular);
