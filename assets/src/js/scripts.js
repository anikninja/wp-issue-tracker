/**
 * Frontend Scripts.
 *
 * @version 1.0.0
 */

// import '../scss/styles.scss';
// import $ from 'jquery';

;(function ($, window, document, undefined) {
    "use strict";
    const identifier = 'issue-tracker-modal';
    $(document)
        .on('click', '.open-' + identifier, function (event) {
            event.preventDefault();
            let modal = $($(this).data('modal'));
            if (modal.length) {
                modal.show();
            }
        })
        .on('click', '.close', function (event) {
            event.preventDefault();
            $(this).closest('.' + identifier).hide();
        });
    $(window).on('click', function (event) {
        let target = $(event.target);
        if (target.hasClass(identifier)) {
            target.hide();
        }
    });
})(jQuery, window, document);