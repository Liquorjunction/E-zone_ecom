(function (window, $) {
    'use strict';

    function initSelect2Ajax(scope) {
        if (typeof $ === 'undefined' || !$.fn || !$.fn.select2) {
            return;
        }

        function syncDependencyState($child, $parent, forceClear) {
            if (!$child || !$child.length || !$parent || !$parent.length) {
                return;
            }

            var hasParentValue = !!$parent.val();
            $child.prop('disabled', !hasParentValue);
            if (forceClear || !hasParentValue) {
                $child.val(null).trigger('change');
            }
        }

        var $scope = scope ? $(scope) : $(document);
        var $elements = $scope.find('.select2-ajax');

        $elements.each(function () {
            var $el = $(this);

            if ($el.data('select2')) {
                return;
            }

            var ajaxUrl = $el.data('option-url') || $el.data('url');
            if (!ajaxUrl) {
                return;
            }

            var allowClear = String($el.data('allow-clear')) === 'true';
            var placeholder = $el.data('placeholder') || 'Select option';
            var dependsOn = $el.data('depends-on');
            var currentDependsOn = dependsOn;
            var dependencyParam = $el.data('dependency-param') || 'parent_id';
            var $dependency = dependsOn ? $(dependsOn) : null;

            window.app = window.app || {};
            app.options = app.options || {};
            app.options.select2 = app.options.select2 || {};
            app.options.select2.ajax = app.options.select2.ajax || {};
            app.options.select2.ajax.ajax = app.options.select2.ajax.ajax || {};

            app.options.select2.ajax.ajax.url = ajaxUrl;
            app.options.select2.ajax.allowClear = allowClear;
            app.options.select2.ajax.minimumInputLength = 0;
            app.options.select2.ajax.placeholder = placeholder;
            app.options.select2.ajax.width = '100%';
            app.options.select2.ajax.ajax.data = function (params) {
                var payload = {
                    q: params.term,
                };

                if ($dependency && $dependency.length) {
                    payload[dependencyParam] = $dependency.val() || '';
                }

                return payload;
            };
            app.options.select2.ajax.ajax.processResults = function (data) {
                if (Array.isArray(data)) {
                    return { results: data };
                }

                return {
                    results: data && data.results ? data.results : [],
                };
            };

            $el.select2(app.options.select2.ajax);

            if ($dependency && $dependency.length) {
                if (!$dependency.data('select2AjaxBound')) {
                    $dependency.data('select2AjaxBound', true);
                    $dependency.on('change', function () {
                        $elements.filter(function () {
                            return $(this).data('depends-on') === currentDependsOn;
                        }).each(function () {
                            var $child = $(this);
                            syncDependencyState($child, $dependency, true);
                        });
                    });
                }

                syncDependencyState($el, $dependency, false);
            }
        });
    }

    $(function () {
        initSelect2Ajax(document);
    });

    window.initSelect2Ajax = initSelect2Ajax;
}(window, window.jQuery));
