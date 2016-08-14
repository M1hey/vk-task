<head>
    <link href="/css/employer.css" rel="stylesheet" media="screen"/>
    <script src="/js/employer.js" type="text/javascript"></script>
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
            <div id="account-info" class="col-md-offset-2 col-md-3 col-sm-6">
                <h2>Aккаунт: </h2>
                <div class="sidebar">
                    <div class="row account-state">
                        <div class="row">
                            <h3 class="account-name col-md-8 col-md-offset-1">
                                <?php global $account_name;
                                echo $account_name; ?></h3>
                            <label class="col-md-6 col-md-offset-1 col-sm-6 col-sm-offset-2"
                                   for="acc_balance">Баланс:</label>
                            <div id="acc_balance" class="col-md-2 col-sm-2 text-right">
                                <?php global $acc_balance;
                                echo $acc_balance . '$'; ?></div>
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
                        <button id="create-order-button" class="btn btn-link">Создать задачу</button>
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
                            <?php
                            global $orders;
                            if ($orders && count($orders)) {
                                global $order_amount, $order_title;
                                echo "<h2 class=\"orders-title\">Ваши заказы:</h2>";

                                foreach ($orders as $order) {
                                    $order_title = $order['title'];
                                    $order_amount = $order['reward'];

                                    include 'order_view.php';
                                }
                            } else {
                                echo "<h2 class=\"orders-title\">У вас пока нет размещённых заказов</h2>";
                            }
                            ?>
                        </div>
                        <div id="emloyer-order-add-form-wrapper">
                            <button id="close-form-button" class="btn btn-link">x</button>
                            <h2>Информация о заказе</h2>
                            <form class="add-order-form form-narrow form-horizontal" method="post" action="add_order"
                                  accept-charset="UTF-8">
                                <div class="alert alert-danger form-group" role="alert">
                                    <span class="sr-only">Error:</span>
                                    <div id="order_err_msg"></div>
                                </div>
                                <fieldset>
                                    <div class="form-group">
                                        <input name="title" type="text" class="form-control"
                                               placeholder="Краткое описание" value="Трудная задача"/>
                                    </div>
                                    <div class="form-group">
                                        <input name="amount" type="text" class="form-control" placeholder="Стоимость"
                                               value="150"/>
                                    </div>
                                    <div class="form-group">
                                        <button id="place_order_btn" type="submit" class="btn btn-default btn-sm btn-block btn-success" data-loading-text="Подождите">
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