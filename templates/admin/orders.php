<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function ristopos_display_orders()
{
    do_action('ristopos_before_page_content');

    // Retrieve orders
    $orders = wc_get_orders(array(
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'type' => 'shop_order'
    ));

    // Start output buffer
    ob_start();
?>
    <div class="wrap">
        <h1><?php echo esc_html__('RistoPOS Orders', 'ristopos'); ?></h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Order Number', 'ristopos'); ?></th>
                    <th><?php echo esc_html__('Date', 'ristopos'); ?></th>
                    <th><?php echo esc_html__('Total', 'ristopos'); ?></th>
                    <th><?php echo esc_html__('Waiter', 'ristopos'); ?></th>
                    <th><?php echo esc_html__('Product Notes', 'ristopos'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <?php
                    $order_id = $order->get_id();
                    $date = $order->get_date_created()->date_i18n('Y-m-d H:i:s');
                    $total = $order->get_total();

                    $user_id = method_exists($order, 'get_customer_id') ? $order->get_customer_id() : 0;
                    $user = get_user_by('id', $user_id);
                    $username = $user ? $user->user_login : esc_html__('N/A', 'ristopos');

                    // Retrieve product notes
                    $notes = '';
                    foreach ($order->get_items() as $item_id => $item) {
                        $product_note = wc_get_order_item_meta($item_id, 'Note', true);
                        if (!empty($product_note)) {
                            $notes .= $item->get_name() . ': ' . $product_note . '<br>';
                        }
                    }
                    $notes = !empty($notes) ? $notes : esc_html__('N/A', 'ristopos');
                    ?>
                    <tr>
                        <td><?php echo esc_html($order_id); ?></td>
                        <td><?php echo esc_html($date); ?></td>
                        <td><?php echo wc_price($total); ?></td>
                        <td><?php echo esc_html($username); ?></td>
                        <td><?php echo wp_kses_post($notes); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
    // End output buffer and return content
    return ob_get_clean();
}

function ristopos_orders_styles()
{
?>
    <style>
        .ristopos-div-button {
            display: flex;
            gap: 5px;
        }

        .wrap {
            padding: 10px 20px 0px 20px;
        }

        .ristopos-header {
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ristopos-button {
            background-color: #0073aa;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        @media screen and (max-width: 600px) {
            /* Adjustments for small screens */
        }

        /* Style for the notes column */
        .wp-list-table td:nth-child(5) {
            max-width: 200px;
            word-wrap: break-word;
        }
    </style>
<?php
}

echo ristopos_display_orders();
ristopos_orders_styles();
