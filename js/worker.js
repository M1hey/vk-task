var user_balance = 0;

$(document).ready(function () {
    $(".complete-btn").click(function () {
        $(this).button('loading');
    });

    $(".worker_order_form").submit(function (event) {
        event.preventDefault();

        var form = $(this);

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function (result) {
                console.log(result);
                if (result['success']) {
                    update_worker_feed(form);
                    update_user_balance(result['new_balance']);
                    if (result['system_balance']) {
                        update_system_balance(result['system_balance']);
                    }
                    show_complete_order_success("Вы получили " + result['reward'] + "$");
                } else {
                    // it could require relogin. Whatever
                    update_worker_feed(form);
                    show_complete_order_error(result['msg']);
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

function update_worker_feed(form) {
    form.remove();
    if ($(".order").length > 0) {
        $(".orders-title").text("Доступные заказы");
    } else {
        $(".orders-title").text("Нет доступных заказов");
    }
}

function show_complete_order_success(msg) {
    $('#worker-orders').find('.alert').css('display', 'block');
    $('#worker-orders').find('.alert').removeClass('alert-danger').addClass('alert-success');
    $('#worker-orders').find('.alert-msg').text(msg);
}

function show_complete_order_error(msg) {
    $('#worker-orders').find('.alert').css('display', 'block');
    $('#worker-orders').find('.alert').removeClass('alert-success').addClass('alert-danger');
    $('#worker-orders').find('.alert-msg').text(msg);
}

function update_feed_content(html) {
    // $('.orders-title').after(html);
    // $('.orders-title').text("Ваши заказы:");
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