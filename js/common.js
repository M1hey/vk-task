$(document).ready(function () {
    $("#add_err").css('display', 'none', 'important');
});

function update_page_content(html) {
    $('#content-wrapper').html(html);
}

function change_page_url(title, url) {
    history.pushState(null, title, url);
}