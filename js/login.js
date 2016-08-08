$(document).ready(function () {
    $(".login-form").submit(function (event) {
        event.preventDefault();

        var form = $(this);

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function (data) {
                if (data) {
                    update_page_content(data);
                } else {
                    show_login_error("Неправильный логин или пароль");
                }
            },
            error: function (qxXHR, status, error) {
                msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
                show_login_error(msg);
            },
            beforeSend: function () {
                // TODO show loading state
            }
        });
        return false;
    });
});

function show_login_error(msg) {
    $(".alert").css('display', 'block', 'important');
    $("#err_msg").html(msg);
}