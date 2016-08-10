<script type="text/javascript">
    // looks like it's also controller's part
    $(document).ready(function () {
        update_user_balance(<?php global $acc_balance; echo $acc_balance; ?>);
    });
</script>
<div class="order panel panel-primary">
    <div class="panel-heading">
        <?php global $order_title, $order_amount;
        echo "<span><b>" . $order_title . "</b>:\t" . $order_amount . '$</span>'; ?>
        <button class="btn btn-link disabled">X</button>
    </div>
</div>