<head>
    <link href="/css/order.css" rel="stylesheet" media="screen"/>
    <script src="/js/order.js" type="text/javascript"></script>
</head>
<script type="text/javascript">
    $(document).ready(function () {
        change_page_url('Страница пользователя', '/user');
        update_user_balance(<?php global $acc_balance; echo $acc_balance; ?>);
    });
</script>
<div class="container">
    <div class="row header">
        <h1><a href="/"><span>Лента</span> <span>Заказов</span></a></h1>
    </div>
    <div class="row page container container-fluid">
        <div class="row">
            <div class="col-md-offset-2 col-md-3 col-sm-6 well">
                <div class="sidebar">
                    <h3 class="account-name text-center">
                        <?php global $account_name;
                        echo $account_name; ?></h3>
                    <div class="row account-state">
                        <div class="row">
                            <label class="col-md-6 col-md-offset-1 col-sm-6 col-sm-offset-2"
                                   for="acc_balance">Баланс:</label>
                            <div id="acc_balance" class="col-md-2 col-sm-2">
                                <?php global $acc_balance;
                                echo $acc_balance; ?></div>
                        </div>
                        <div class="row">
                            <label class="col-sm-6 col-md-offset-1 col-sm-6 col-sm-offset-2 text-nowrap"
                                   for="sys_balance">Баланс системы:</label>
                            <div id="sys_balance" class="col-md-2 col-sm-2">
                                <?php global $sys_balance;
                                echo $sys_balance; ?></div>
                        </div>
                    </div>
                    <div class="action-button bordered-top text-center">
                        <button id="create-order-button" class="btn btn-primary btn-sm">Создать задачу</button>
                        <a href="./?logout">
                            <button class="btn btn-link" type="submit">Выйти</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="content col-offset-md-1 col-md-11">
                    <div class="box">
                        <div id="emloyer-orders">
                            <h2>
                                У вас пока нет размещённых заказов
                            </h2>
                        </div>
                        <div id="emloyer-order-add-form-wrapper">
                            <button id="close-form-button" class="btn btn-link">x</button>
                            <h2>Информация о заказе</h2>
                            <form class="add-order-form form-narrow form-horizontal" method="post" action="add_order">
                                <div class="alert alert-danger form-group" role="alert">
                                    <span class="sr-only">Error:</span>
                                    <div id="order_err_msg"></div>
                                </div>
                                <fieldset>
                                    <div class="form-group">
                                        <input name="title" type="text" class="form-control"
                                               placeholder="Краткое описание"/>
                                    </div>
                                    <div class="form-group">
                                        <input name="amount" type="text" class="form-control" placeholder="Стоимость"/>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-default btn-sm btn-block btn-success">
                                            Разместить заказ
                                        </button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>