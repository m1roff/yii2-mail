(function () {

    // block button on click
    $('.mail-btn-form-submit').on('click', function (e) {
        $('.mail-btn-form-submit').attr('disabled', true);
        $(this).parents('form').submit();
        return true;
    });

    $(document).ready(function () {
        $('[data-toggle="popover"]').popover({
            html:true
        });
    });
})();