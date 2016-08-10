var user_balance = 0;

function update_user_balance(new_user_balance) {
    user_balance = new_user_balance;
    $("#acc_balance").html(user_balance);
}

$(document).ready(function () {
    $("#create-order-button").click(function (event) {
        show_add_order_form(true);
    });
    $("#close-form-button").click(function () {
        show_add_order_form(false);
    });

    var form_selector = $("#emloyer-order-add-form-wrapper").find("form");
    override_form_submit({
        form_selector: form_selector,
        success: function (data) {
            console.log(data);
            if (data) {
                show_add_order_form(false);
                update_feed_content(data);
                // todo update balance
            } else {
                show_order_error("Ошибка ввода");
            }
        },
        error: function (qxXHR, status, error) {
            msg = ("" == error) ? "Сервер недоступен" : status + ": " + error;
            show_order_error(msg);
        },
        validation: function () {
            var title = form_selector.find('input[name="title"]').val();
            if (title === '') {
                show_order_error("Введите наименование");
                return false;
            }
            var amount = form_selector.find('input[name="amount"]').val();
            if (amount === '') {
                show_order_error("Введите стоимость");
                return false;
            }
            if (isNaN(amount) || amount <= 0) {
                show_order_error("Стоимость заказа заказа должна быть числом больше 0");
                return false;
            }
            if (amount > user_balance) {
                show_order_error("У вас недостаточно средств, чтобы разместить заказ");
                return false;
            }

            return true;
        }
    });
});

function update_feed_content(html) {
    $('#emloyer-orders').first().insertBefore(html);
}


function show_add_order_form(show_form) {
    $("#emloyer-order-add-form-wrapper").toggle(show_form);
    $("#emloyer-orders").toggle(!show_form);
    if (show_form) {
        hide_order_error();
    }
}

function hide_order_error() {
    $("#emloyer-order-add-form-wrapper").find("form").find(".alert").css('display', 'none');
}

function show_order_error(msg) {
    $("#emloyer-order-add-form-wrapper").find("form").find(".alert").css('display', 'block', 'important');
    $("#order_err_msg").html(msg);
}