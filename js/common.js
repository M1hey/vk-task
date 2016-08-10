var user_balance = 0;
var system_balance = 0;

function update_system_balance(new_balance) {
    system_balance = new_balance;
    $("#sys_balance").html(system_balance + '$');
}

function update_user_balance(new_user_balance) {
    user_balance = new_user_balance;
    $("#acc_balance").html(user_balance + '$');
}

$(document).ready(function () {
    $("#add_err").css('display', 'none');
});

function update_page_content(html) {
    $('#content-wrapper').html(html);
}

function change_page_url(title, url) {
    history.pushState(null, title, url);
}

function override_form_submit(options) {
    options['form_selector'].submit(function (event) {
        event.preventDefault();

        var form = $(this);

        if (options['validation']()) {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: options['success'],
                error: options['error'],
                beforeSend: options['before_send']
            })
        }

        return false;
    });
}