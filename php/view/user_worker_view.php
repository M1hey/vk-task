<head>
    <link href="/css/worker.css" rel="stylesheet" media="screen"/>
    <script src="/js/worker.js" type="text/javascript"></script>
</head>
<script type="text/javascript">
    $(document).ready(function () {
        change_page_url('Страница пользователя', '/user');
        update_user_balance('<?php global $acc_balance; echo format_money($acc_balance); ?>');
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
                        </div>
                        <div class="row">
                            <label class="col-md-6 col-md-offset-1 col-sm-6 col-sm-offset-2"
                                   for="acc_balance">Баланс:</label>
                            <div class="col-md-2 col-sm-2 text-right group-space-right">
                                <div id="acc_balance">
                                    <?php global $acc_balance;
                                    echo format_money($acc_balance) . '$'; ?>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <span id="update_acc_balance_btn" class="glyphicon glyphicon-refresh"
                                      aria-hidden="false"></span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-6 col-md-offset-1 col-sm-6 col-sm-offset-2 text-nowrap"
                                   for="sys_balance">Баланс системы:</label>
                            <div id="sys_balance" class="col-md-2 col-sm-2">
                                <?php global $sys_balance;
                                echo format_money($sys_balance) . '$'; ?></div>
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
                    <div id="orders-wrapper" class="box">
                        <?php
                        global $orders;
                        if ($orders && count($orders)) {
                            echo "<h2 class=\"orders-title\">Доступные заказы:</h2>";
                        } else {
                            echo "<h2 class=\"orders-title\">Нет доступных заказов</h2>";
                        } ?>
                        <div class="alert" role="alert">
                            <button type="button" class="close" onclick="$('.alert').hide()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <span class="alert-msg"></span>
                        </div>
                        <div id="worker-orders">
                            <?php
                            global $orders;
                            if ($orders && count($orders)) {
                                global $order_id, $order_amount, $order_title, $order_employer;

                                foreach ($orders as $order) {
                                    $order_id = $order['id'];
                                    $order_title = $order['title'];
                                    $order_amount = $order['reward'];
                                    $order_employer = $order['employer_name'];

                                    include 'order_worker_view.php';
                                }
                            }
                            ?>
                        </div>
                        <div class="text-center">
                            <button id="load_more_btn" class="btn btn-link" data-loading-text="Загрузка ...">Загрузить
                                ещё
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>