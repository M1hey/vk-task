<div class="loginform-in">
    <h1>User Login</h1>
    <div class="err" id="add_err"></div>
    <fieldset>
        <!--        <form action="./" method="post">-->
        <!--            <ul>-->
        <!--                <li><label for="name">Username </label>-->
        <!--                    <input type="text" size="30" name="name" id="name"/></li>-->
        <!--                <li><label for="name">Password</label>-->
        <!--                    <input type="password" size="30" name="word" id="word"/></li>-->
        <!--                <li><label></label>-->
        <!--                    <input type="submit" id="login" name="login" value="Login" class="loginbutton"></li>-->
        <!--            </ul>-->
        <!--        </form>-->
        <form class="form-narrow form-horizontal" method="post" action="login.php">
            <!--TODO show errors, info-->
            <fieldset>
                <legend>Please login</legend>
                <div class="form-group">
                    <label for="inputLogin" class="col-lg-4 control-label">Логин</label>

                    <div class="col-lg-8">
                        <input type="text" class="form-control" id="inputLogin" placeholder="Логин" name="login"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-lg-4 control-label">Пароль</label>

                    <div class="col-lg-8">
                        <input type="password" class="form-control" id="inputPassword" placeholder="Пароль"
                               name="pwd"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-8">
                        <button type="submit" class="btn btn-default btn-primary">Login</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </fieldset>
</div>