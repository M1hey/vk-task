<script type="text/javascript">
    // looks like it's also controller's part
    $(document).ready(function () {
        update_user_balance(<?php global $acc_balance; echo $acc_balance; ?>);
    });
</script>
<form class="worker_order_form" method="post" action="complete_order">
    <div class="order bordered-top">
        <div class="row">
            <div class="order-description col-md-8"><?php global $order_id, $order_title, $order_amount;
                echo "<span>" . $order_title . ":\t<b>" . $order_amount . '$</b></span>'; ?></div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-link pull-right">Выполнить</button>
                <input name="order_id" type="hidden" value="<?php global $order_id;
                echo $order_id ?>"
            </div>
        </div>
    </div>
</form>