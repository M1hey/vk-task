$(document).ready(function () {
    override_form_submit({
        form_selector: $(".login-form"),
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
        validation: function () {
            return true;
        }
    });
});

function show_login_error(msg) {
    $(".alert").css('display', 'block');
    $("#err_msg").html(msg);
}