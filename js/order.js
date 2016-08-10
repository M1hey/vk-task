$(document).ready(function () {
    $("#create-order-button").click(function (event) {
        show_add_order_form(true);
    });
    $("#close-form-button").click(function () {
        show_add_order_form(false);
    });
});

function show_add_order_form(show_form) {
    $("#emloyer-order-add-form-wrapper").toggle(show_form);
    $("#emloyer-orders").toggle(!show_form);
}

function show_login_error(msg) {
    $(".alert").css('display', 'block', 'important');
    $("#err_msg").html(msg);
}