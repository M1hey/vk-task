<head>
    <link href="/css/login.css" rel="stylesheet" media="screen"/>
    <script src="/js/login.js" type="text/javascript"></script>
</head>
<script type="text/javascript">
    // looks like it's also controller's part
    $(document).ready(function () {
        change_page_url('Вход в систему', '/login');
    });
</script>
<div class="container">
    <div class="row">
        <h1>Вход в систему</h1>
        <div class="login-panel">
            <form class="login-form form-narrow form-horizontal" method="post" action="login">
                <div class="alert alert-danger form-group" role="alert">
                    <span class="sr-only">Error:</span>
                    <div id="err_msg"></div>
                </div>
                <fieldset>
                    <div class="form-group">
                        <input name="login" type="text" class="form-control" placeholder="Логин"/>
                    </div>
                    <div class="form-group">
                        <input name="password" type="password" class="form-control" placeholder="Пароль"/>
                    </div>
                    <div class="form-group">
                        <button id="login_button" type="submit" class="btn btn-link btn-block" data-loading-text="Загрузка">Войти</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>