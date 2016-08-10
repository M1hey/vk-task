var user_balance = 0;

$(document).ready(function () {
    var form_selector = $(".worker_order_form");
    override_form_submit({
        form_selector: form_selector,
        success: function (data) {
            console.log(data);
            if (data) {
                // todo remove order
                // show_add_order_form(false);
                // update_feed_content(data);
            } else {
                show_complete_order_error("Ошибка ввода");
            }
        },
        error: function (qxXHR, status, error) {
            msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
            show_complete_order_error(msg);
        },
        validation: function () {
            return true;
        }
    });
});

function update_feed_content(html) {
    // $('.orders-title').after(html);
    // $('.orders-title').text("Ваши заказы:");
}

function show_complete_order_error(msg) {
    console.log(msg);
}

// function show_add_order_form(show_form) {
//     $("#emloyer-order-add-form-wrapper").toggle(show_form);
//     $("#emloyer-orders").toggle(!show_form);
//     if (show_form) {
//         hide_order_error();
//     }
// }
//
// function hide_order_error() {
//     $("#emloyer-order-add-form-wrapper").find("form").find(".alert").css('display', 'none');
// }
//
// function show_order_error(msg) {
//     $("#emloyer-order-add-form-wrapper").find("form").find(".alert").css('display', 'block');
//     $("#order_err_msg").html(msg);
// }