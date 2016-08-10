$(document).ready(function () {
    override_form_submit({
        form_selector: $(".login-form"),
        success: function (data) {
            if (data) {
                if (data) {
                    update_page_content(data);
                } else {
                    show_login_error("Неправильный логин или пароль");
                }
            } else {
                show_login_error("Неправильный логин или пароль");
            }
        },
        error: function (qxXHR, status, error) {
            msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
            show_login_error(msg);
        }
    });
});

function show_login_error(msg) {
    $(".alert").css('display', 'block', 'important');
    $("#err_msg").html(msg);
}