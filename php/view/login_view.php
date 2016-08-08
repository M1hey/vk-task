<head>
    <link href="/css/login.css" rel="stylesheet" media="screen"/>
</head>
<div class="container">
    <div class="row">
        <h1>Вход в систему</h1>
        <div class="login-form">
            <div class="err" id="add_err"></div>
            <fieldset>
                <form class="form-narrow form-horizontal" method="post" action="login.php">
                    <!--TODO show errors, info-->
                    <fieldset>
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputLogin" placeholder="Логин"
                                   name="login"/>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="inputPassword" placeholder="Пароль"
                                   name="password"/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default btn-primary btn-sm btn-block">Login</button>
                        </div>
                    </fieldset>
                </form>
            </fieldset>
        </div>
    </div>
</div>