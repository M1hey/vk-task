$(document).ready(function () {
    $("#add_err").css('display', 'none', 'important');
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

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: options['success'],
            error: options['error'],
            beforeSend: options['before_send']
        });
        return false;
    });
}