(function(angular) {
    var module = angular.module('civicase');
    var $ = angular.element;

    /**
     * Directive for the sticky table header functionality on case list page
     */
    module.directive('civicaseStickyTableHeader', function($rootScope, $timeout) {
        return {
            restrict: 'A',
            link: civicaseStickyTableHeaderLink
        };

        /**
         * Link function for stickyTableHeader Directive
         *
         * @param {object} scope
         *   Scope under which directive is called
         * @param {object} $el
         *   Element on which directive is called
         * @param {object} attrs
         *   attributes of directive
         */
        function civicaseStickyTableHeaderLink(scope, $el, attrs) {
            var $header = $el.find('thead');
            var $element = $header;

            function affixElementExtras() {
                $('th', $element).each(function() {
                    $(this).css('min-width', $(this).outerWidth() + 'px');
                });
                $element.find('th:first').css('position', 'static');
            }

            function unAffixElementExtras() {
                // $element.find('th:first').css('position', 'absolute');
            }

            function affixElement() {
                if (!$element.data('affixed')) {
                    $element.data('affixed', true);
                    var bodyPadding = parseInt($('body').css('padding-top'), 10); // to see the space for fixed menus
                    $element.css('position', 'fixed');
                    $element.css('top', bodyPadding + 'px');
                    $element.data('originalOffsetLeft', $element.offset().left);
                    $element.css('left', 0);
                    $element.data('originalZIndex', $element.css("z-index"));
                    $element.css('z-index', 10000);
                    affixElementExtras();
                }
            }

            function unAffixElement() {
                if ($element.data('affixed')) {
                    $element.removeData('affixed');
                    $element.css('position', 'static');
                    var originalOffsetTop = $element.data('originalOffsetLeft');
                    $element.css('top', originalOffsetTop + 'px');
                    var originalOffsetLeft = $element.data('originalOffsetTop');
                    $element.css('left', originalOffsetLeft + 'px');
                    var originalZIndex = $element.data('originalZIndex');
                    $element.css('z-index', originalZIndex);
                    unAffixElementExtras();
                }
            }

            var delay = (function() {
                var timer = 0;
                return function(callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();
            $(document).scroll(function() {
                delay(function() {
                    checkForAffixingElement();
                }, 10);
            });

            function checkForAffixingElement() {
                if (!$element.data('originalOffsetTop')) {
                    $element.data('originalOffsetTop', $element.offset().top);
                    $element.data('originalHeight', $element.height());
                }

                // If the header is currently affixed, then that means we need to subtract the height of the header from the offset top to get the offset we should be checking
                if ($element.data('affixed')) {
                    var offsetTopCheck = $element.data('originalOffsetTop') - $element.data('originalHeight');
                }
                else {
                    var offsetTopCheck = $element.data('originalOffsetTop');
                }

                var windowOffsetTop = window.scrollY;

                if (offsetTopCheck < windowOffsetTop) {
                    affixElement();
                }
                else {
                    unAffixElement();
                }
            }
        }
    });
})(angular);
