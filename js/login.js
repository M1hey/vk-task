$(document).ready(function () {
    $(".login-form").submit(function (event) {
        event.preventDefault();

        var form = $(this);

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function (html) {
                if (html) {
                    update_page_content(html);
                } else {
                    // TODO error handling
                    $("#add_err").css('display', 'inline', 'important');
                    $("#add_err").html("<img src='images/alert.png' />Wrong username or password");
                }
            },
            beforeSend: function () {
                // TODO show loading state
                $("#add_err").css('display', 'inline', 'important');
                $("#add_err").html("<img src='images/ajax-loader.gif' /> Loading...")
            }
        });
        return false;
    });
});
