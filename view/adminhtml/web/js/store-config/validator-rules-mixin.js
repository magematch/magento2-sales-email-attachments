define(['jquery'], function ($) {
    'use strict';

    return function (target) {
        $.validator.addMethod(
            'validate-file',
            function (value) {
                if (value === '') {
                    return true;
                }
                var match = value.match(/\.([^.]+)$/);
                if (!match) {
                    return false;
                }
                return ['pdf', 'doc', 'docx', 'txt'].indexOf(match[1].toLowerCase()) !== -1;
            },
            $.mage.__('Please select a valid file type (pdf, doc, docx, txt).')
        );

        return target;
    };
});
