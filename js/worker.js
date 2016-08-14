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
                result = JSON.parse(result);
                if (result['success']) {
                    update_worker_feed(form);
                    update_user_balance(user_balance + result['reward']);
                    if (result['system_balance']) {
                        update_system_balance(result['system_balance']);
                    }
                    show_complete_order_success("Вы получили " + result['reward'] + "$");
                } else {
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
}

function show_complete_order_success(msg) {
    $('#emloyer-orders').find('.alert').css('display', 'block');
    $('#emloyer-orders').find('.alert').removeClass('alert-danger').addClass('alert-success');
    $('#emloyer-orders').find('.alert-msg').text(msg);
}

function show_complete_order_error(msg) {
    $('#emloyer-orders').find('.alert').css('display', 'block');
    $('#emloyer-orders').find('.alert').removeClass('alert-success').addClass('alert-danger');
    $('#emloyer-orders').find('.alert-msg').text(msg);
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