<head>
    <link href="/css/worker.css" rel="stylesheet" media="screen"/>
    <script src="/js/worker.js" type="text/javascript"></script>
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
                        <span></span>
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
                            if (count($orders)) {
                                global $order_id, $order_amount, $order_title;
                                echo "<h2 class=\"orders-title\">Доступные заказы:</h2>";

                                foreach ($orders as $order) {
                                    $order_title = $order['title'];
                                    $order_amount = $order['reward'];
                                    $order_id = $order['id'];

                                    include 'worker_order_view.php';
                                }
                            } else {
                                echo "<h2 class=\"orders-title\">Нет доступных заказов</h2>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>