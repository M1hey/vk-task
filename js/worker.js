var user_balance = 0;

$(document).ready(function () {
    var form_selector = $(".worker_order_form");
    $(".worker_order_form").submit(function (event) {
        event.preventDefault();

        var form = $(this);

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function (result) {
                console.log(result);
                result = JSON.parse(result);
                if (result['success']) {
                    // TODO get JSON with all result
                    update_worker_feed(form);
                    update_user_balance(user_balance + result['received_amount']);
                    // TODO update_system_balance();
                    show_complete_order_success("Вы получили " + result['received_amount'] + "$");
                } else {
                    show_complete_order_error("Ошибка ввода");
                }
            },
            error: function (qxXHR, status, error) {
                msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
                show_complete_order_error(msg);
            }
        });

        return false;
    });
});

function update_worker_feed(form_selector) {
    form_selector.remove();
}

function show_complete_order_success(msg) {
    console.log(msg);
}

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