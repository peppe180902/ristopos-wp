<?php
if (!defined('ABSPATH')) {
    exit; // Uscita se accesso diretto
}

function ristopos_display_orders() {
    do_action('ristopos_before_page_content');

    $orders = wc_get_orders(array(
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'type' => 'shop_order'
    ));

    echo '<div class="wrap">';
    echo '<h1>Ordini RistoPOS</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th>Numero Ordine</th>';
    echo '<th>Data</th>';
    echo '<th>Totale</th>';
    echo '<th>Cameriere</th>';
    echo '<th>Note Prodotti</th>'; // Nuova colonna per le note
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $date = $order->get_date_created()->date_i18n('Y-m-d H:i:s');
        $total = $order->get_total();
        
        $user_id = method_exists($order, 'get_customer_id') ? $order->get_customer_id() : 0;
        $user = get_user_by('id', $user_id);
        $username = $user ? $user->user_login : 'N/A';

        // Recupera le note dei prodotti
        $notes = '';
        foreach ($order->get_items() as $item_id => $item) {
            $product_note = wc_get_order_item_meta($item_id, 'Note', true);
            if (!empty($product_note)) {
                $notes .= $item->get_name() . ': ' . $product_note . '<br>';
            }
        }
        $notes = !empty($notes) ? $notes : 'N/A';

        echo '<tr>';
        echo '<td>' . $order_id . '</td>';
        echo '<td>' . $date . '</td>';
        echo '<td>' . wc_price($total) . '</td>';
        echo '<td>' . $username . '</td>';
        echo '<td>' . $notes . '</td>'; // Visualizza le note dei prodotti
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

function ristopos_orders_styles() {
    echo '
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
            // #wpbody {
            //     padding-top: 0px;
            // }
        }
        /* Stile per la colonna delle note */
        .wp-list-table td:nth-child(5) {
            max-width: 200px;
            word-wrap: break-word;
        }
    </style>
    ';
}

ristopos_display_orders();
ristopos_orders_styles();