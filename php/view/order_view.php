<div class="order bordered-top">
        <button class="btn btn-link disabled">X</button>
        <?php global $order_title, $order_amount;
        echo "<span>" . $order_title . ":\t" . number_format($order_amount / 100, 2, '.', '') . '$</span>'; ?>
</div>