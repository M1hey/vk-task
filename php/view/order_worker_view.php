<form class="worker_order_form" method="post" action="complete_order">
    <div class="order bordered-top">
        <div class="row">
            <div class="order-description col-md-8">
                <?php
                global $order_id, $order_title, $order_amount, $order_employer;
                echo "<span>" . $order_id . ":\t" . $order_employer . "<br/>";
                echo $order_title . ":\t<b>" . number_format($order_amount / 100, 2, '.', '') . "$</b></span>";
                ?>
            </div>
            <div class="col-md-4">
                <button type="submit" class="complete-btn btn btn-link pull-right" data-loading-text="Выплонение">Выполнить</button>
                <input name="order_id" type="hidden" value="<?php global $order_id;
                echo $order_id ?>"/>
            </div>
        </div>
    </div>
</form>