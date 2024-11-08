jQuery(document).ready(function ($) {

    let notice = $('.notice');

    $('input').on('focus', function () {
        notice.stop(true, true).fadeIn('fast');
    });
});




