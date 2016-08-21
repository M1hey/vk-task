var user_balance = 0;
var last_order_id = 0;

$(document).ready(function () {
    create_load_more_handler();
    create_update_acc_balance_handler();
    override_orders_submit();
});

function create_update_acc_balance_handler() {
    $("#update_acc_balance_btn").click(function () {
        $.ajax({
            type: 'GET',
            url: 'update_acc_balance',
            success: function (result, status, jqXHR) {
                console.log(result);
                if ('application/json' == jqXHR.getResponseHeader("content-type")) {
                    if (result['success']) {
                        update_user_balance(result['new_balance']);
                    } else {
                        show_orders_error(result['msg']);
                    }
                } else {
                    show_orders_error("Сервер недоступен");
                }
            },
            error: function (qxXHR, status, error) {
                msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
                show_orders_error(msg);
            }
        });
    });
}

function create_load_more_handler() {
    $("#load_more_btn").click(function () {
        var load_more_btn = $(this);
        load_more_btn.button('loading');

        $.ajax({
            type: 'POST',
            url: 'load_more_orders',
            data: 'last_order_id=' + last_order_id,
            success: function (result, status, jqXHR) {
                console.log(result);
                load_more_btn.button('reset');
                if ('application/json' == jqXHR.getResponseHeader("content-type")) {
                    if (result['success']) {
                        // todo no more orders msg
                        if (result['more_orders']) {
                            handle_load_orders(result['more_orders']);
                            update_orders_title();
                        } else {
                            show_orders_msg("Заказов больше нет. Обновите позже", 'info');
                        }
                    } else {
                        show_orders_error(result['msg']);
                    }
                } else {
                    show_orders_error("Сервер недоступен");
                }
            },
            error: function (qxXHR, status, error) {
                load_more_btn.button('reset');
                msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
                show_orders_error(msg);
            }
        });
    });
}

function override_orders_submit() {
    $(".complete-btn").click(function () {
        $(this).button('loading');
    });

    $(".worker_order_form").unbind('submit');
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
                    if (result['msg']) {
                        show_orders_error(result['msg']);
                        // it could require relogin. Whatever
                        update_worker_feed(result['new_orders']);
                    } else {
                        show_orders_error("Невозможно совершить операцию");
                    }
                }
            },
            error: function (qxXHR, status, error) {
                form.find('.complete-btn').button('reset');
                msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
                show_orders_error(msg);
            }
        });

        return false;
    });
}

function update_last_order_id(order_id) {
    if (order_id > last_order_id) {
        last_order_id = order_id;
    }
}

function handle_load_orders(new_orders) {
    $('#worker-orders').append(new_orders);
    override_orders_submit();
}

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
    $('#orders-wrapper').find('.alert').show();
    $('#orders-wrapper').find('.alert').removeClass('alert-danger').addClass('alert-success');
    $('#orders-wrapper').find('.alert-msg').text(msg);
}

function show_orders_error(msg) {
    $('#orders-wrapper').find('.alert').show();
    $('#orders-wrapper').find('.alert').removeClass('alert-success').addClass('alert-danger');
    $('#orders-wrapper').find('.alert-msg').text(msg);
}

function show_orders_msg(msg, level) {
    var alert = $('#orders-wrapper').find('.alert');
    alert.show();
    alert.removeClass('alert-success');
    alert.removeClass('alert-danger');
    alert.removeClass('alert-info');

    switch (level) {
        case 'info':
            alert.addClass('alert-info');
            break;
        case 'success':
            alert.addClass('alert-success');
            break;
        case 'danger':
            alert.addClass('alert-danger');
            break;
    }

    alert.find('.alert-msg').text(msg);
}