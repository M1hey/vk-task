<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>

    <title>Test task</title>

    <link href="/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/css/lib/bootstrap/bootstrap.min.css" rel="stylesheet" media="screen"/>

    <script src="/js/jquery-1.6.2.js" type="text/javascript"></script>
    <script src="/js/common.js" type="text/javascript"></script>
</head>
<body>
<div class="wrapper container">
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
                            <label class="col-md-6 col-md-offset-2 col-sm-6 col-sm-offset-2" for="acc_balance">Баланс:</label>
                            <div id="acc_balance" class="col-md-2 col-sm-2"><?php echo $acc_balance; ?></div>
                        </div>
                        <div class="row">
                            <label class="col-sm-6 col-md-offset-2 col-sm-6 col-sm-offset-2 text-nowrap" for="sys_balance">Баланс системы:</label>
                            <div id="sys_balance" class="col-md-2 col-sm-2"><?php echo $sys_balance; ?></div>
                        </div>
                    </div>
                    <div class="action-button bordered-top text-center">
                        <button class="btn btn-default" type="submit">Создать задачу</button>
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
</body>
</html>