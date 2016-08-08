<?php
//TODO it is controller !!!
$account_name = $_SESSION['username'];
$acc_balance = $_SESSION['balance'];
$sys_balance = $_SESSION['sys_balance'];
?>
<script type="text/javascript">
    $(document).ready(function () {
        change_page_url('Страница пользователя', '/user');
    });
</script>
<div class="container">
    <div class="row header">
        <h1><a href="/"><span>Лента</span> <span>Заказов</span></a></h1>
    </div>
    <div class="row page container container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6 well">
                <div class="sidebar">
                    <h3 class="account-name text-center"><?php echo $account_name; ?></h3>
                    <div class="row account-state">
                        <div class="row">
                            <label class="col-md-6 col-md-offset-2 col-sm-6 col-sm-offset-2"
                                   for="acc_balance">Баланс:</label>
                            <div id="acc_balance" class="col-md-2 col-sm-2"><?php echo $acc_balance; ?></div>
                        </div>
                        <div class="row">
                            <label class="col-sm-6 col-md-offset-2 col-sm-6 col-sm-offset-2 text-nowrap"
                                   for="sys_balance">Баланс системы:</label>
                            <div id="sys_balance" class="col-md-2 col-sm-2"><?php echo $sys_balance; ?></div>
                        </div>
                    </div>
                    <div class="action-button bordered-top text-center">
                        <button class="btn btn-primary" type="submit">Создать задачу</button>
                        <a href="./?logout"
                        <button class="btn btn-link" type="submit">Выйти</button>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="content">
                    <div class="box">
                        <!--?php include 'php/views/' . $content_view; ?-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>