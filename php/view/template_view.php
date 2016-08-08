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
<div class="wrapper">
    <?php include dirname(__DIR__) . "/view/" . $view; ?> <!--TODO is it safe to use include?-->
</div>
</body>
</html>