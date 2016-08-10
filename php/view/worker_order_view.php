<script type="text/javascript">
    // looks like it's also controller's part
    $(document).ready(function () {
        update_user_balance(<?php global $acc_balance; echo $acc_balance; ?>);
    });
</script>
<div class="order bordered-top">
    <div class="row"><div class="col-md-8"><?php global $order_title, $order_amount;
    echo "<span>" . $order_title . ":\t" . $order_amount . '$</span>'; ?></div>
    <div class="col-md-4"><button class="btn btn-success pull-right">Выполнить</button></div></div>
</div>