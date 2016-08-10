<script type="text/javascript">
    // looks like it's also controller's part
    $(document).ready(function () {
        update_user_balance(<?php global $acc_balance; echo $acc_balance; ?>);
    });
</script>
<div class="order bordered-top">
        <button class="btn btn-link disabled">X</button>
        <?php global $order_title, $order_amount;
        echo "<span>" . $order_title . ":\t" . $order_amount . '$</span>'; ?>
</div>