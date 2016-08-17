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
                form.find('.complete-btn').button('reset');
                console.log(result);
                if (result['success']) {
                    handle_order_completed(form);
                    update_user_balance(result['new_balance']);
                    if (result['system_balance']) {
                        update_system_balance(result['system_balance']);
                    }
                    show_complete_order_success("Вы получили " + result['reward'] + "$");
                } else {
                    if(result['msg']) {
                        show_complete_order_error(result['msg']);
                        // it could require relogin. Whatever
                        update_worker_feed(result['new_orders']);
                    } else {
                        show_complete_order_error("Невозможно совершить операцию");
                    }
                }
            },
            error: function (qxXHR, status, error) {
                form.find('.complete-btn').button('reset');
                msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
                show_complete_order_error(msg);
            }
        });

        return false;
    });
});

function update_worker_feed(new_orders) {
    if (new_orders != '') {
        $('#worker-orders').html(new_orders);
        update_orders_title();
    }
}

function update_orders_title() {
    if ($(".order").length > 0) {
        $(".orders-title").text("Доступные заказы");
    } else {
        $(".orders-title").text("Нет доступных заказов");
    }
}

function handle_order_completed(form) {
    form.remove();
    update_orders_title();
}

function show_complete_order_success(msg) {
    $('#orders-wrapper').find('.alert').css('display', 'block');
    $('#orders-wrapper').find('.alert').removeClass('alert-danger').addClass('alert-success');
    $('#orders-wrapper').find('.alert-msg').text(msg);
}

function show_complete_order_error(msg) {
    $('#orders-wrapper').find('.alert').css('display', 'block');
    $('#orders-wrapper').find('.alert').removeClass('alert-success').addClass('alert-danger');
    $('#orders-wrapper').find('.alert-msg').text(msg);
}