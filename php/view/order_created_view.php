<script type="text/javascript">
    // looks like it's also controller's part
    $(document).ready(function () {
        change_page_url('Вход в систему', '/login');
        update_user_balance(<?php global $acc_balance; echo $acc_balance; ?>);
    });
</script>
<div class="order row">
    <div class="row">
        <?php global $order_title;
        echo $order_title; ?>
    </div>
    <div class="row">
        <?php global $order_amount;
        echo $order_amount; ?>
    </div>
</div>