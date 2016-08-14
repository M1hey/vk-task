$(document).ready(function () {
    $('#login_button').on('click', function () {
        $(this).button('loading');
    });

    override_form_submit({
        form_selector: $(".login-form"),
        success: function (data) {
            $('#login_button').button('reset');
            if (data) {
                update_page_content(data);
            } else {
                show_login_error("Неправильный логин или пароль");
            }
        },
        error: function (qxXHR, status, error) {
            $('#login_button').button('reset');
            msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
            show_login_error(msg);
        },
        validation: function () {
            return true;
        },
        before_send: function () {
            console.log('login');
        }
    });
});

function show_login_error(msg) {
    $(".alert").css('display', 'block');
    $("#err_msg").html(msg);
}