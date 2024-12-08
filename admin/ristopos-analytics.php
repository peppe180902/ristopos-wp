<?php
if (!defined('ABSPATH')) {
    exit; // Uscita se accesso diretto
}

// Funzione di controllo per la disponibilità delle funzioni WooCommerce
function ristopos_wc_functions_available() {
    return function_exists('WC') && function_exists('wc_price') && function_exists('wc_get_orders');
}

// Funzione principale per la dashboard analitica

function ristopos_analytics_styles() {
    echo '
    <style>
     .ristopos-div-button {
        display: flex;
        gap: 5px;
    }
    .wrap {
        margin: 10px 20px 0 20px;
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
ristopos-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.ristopos-stat-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
}

.ristopos-big-number {
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

.ristopos-charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
}

.ristopos-chart-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
}

canvas {
    max-width: 100%;
}
    </style>
    ';
}

// Funzione per visualizzare le statistiche principali
function ristopos_display_main_stats() {
    $total_sales = ristopos_get_total_sales();
    $total_orders = ristopos_get_total_orders();
    $average_order_value = ristopos_get_average_order_value();

    echo '<div class="ristopos-stats-grid">';
    echo '<div class="ristopos-stat-card">';
    echo '<h3>Vendite Totali</h3>';
    echo '<p class="ristopos-big-number">' . wc_price($total_sales) . '</p>';
    echo '</div>';
    echo '<div class="ristopos-stat-card">';
    echo '<h3>Ordini Totali</h3>';
    echo '<p class="ristopos-big-number">' . $total_orders . '</p>';
    echo '</div>';
    echo '<div class="ristopos-stat-card">';
    echo '<h3>Valore Medio Ordine</h3>';
    echo '<p class="ristopos-big-number">' . wc_price($average_order_value) . '</p>';
    echo '</div>';
    echo '</div>';
}

// Funzione per ottenere il totale delle vendite
function ristopos_get_total_sales() {
    $args = array(
        'status' => 'completed',
        'limit' => -1,
        'return' => 'ids',
    );
    $orders = wc_get_orders($args);
    $total_sales = 0;
    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        $total_sales += $order->get_total();
    }
    return $total_sales;
}

// Funzione per ottenere il totale degli ordini
function ristopos_get_total_orders() {
    $orders = wc_get_orders(array('status' => 'completed', 'return' => 'ids', 'limit' => -1));
    return count($orders);
}

// Funzione per calcolare il valore medio degli ordini
function ristopos_get_average_order_value() {
    $total_sales = ristopos_get_total_sales();
    $total_orders = ristopos_get_total_orders();
    return $total_orders > 0 ? $total_sales / $total_orders : 0;
}

// Funzione per visualizzare i grafici
function ristopos_display_charts() {
    $best_selling_products = ristopos_get_best_selling_products();
    $sales_by_day = ristopos_get_sales_by_day();

    echo '<div class="ristopos-charts-grid">';
    echo '<div class="ristopos-chart-card">';
    echo '<h3>Prodotti Più Venduti</h3>';
    ristopos_render_bar_chart($best_selling_products);
    echo '</div>';
    echo '<div class="ristopos-chart-card">';
    echo '<h3>Vendite per Giorno</h3>';
    ristopos_render_line_chart($sales_by_day);
    echo '</div>';
    echo '</div>';
}

// Funzione per ottenere i prodotti più venduti
function ristopos_get_best_selling_products() {
    $args = array(
        'post_type' => 'product',
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 5,
        'order' => 'DESC'
    );
    $products = new WP_Query($args);
    $best_selling = array();
    while ($products->have_posts()) {
        $products->the_post();
        $product = wc_get_product(get_the_ID());
        $best_selling[] = array(
            'name' => $product->get_name(),
            'total_quantity' => $product->get_total_sales()
        );
    }
    wp_reset_postdata();
    return $best_selling;
}

// Funzione per ottenere le vendite per giorno
function ristopos_get_sales_by_day() {
    $args = array(
        'status' => 'completed',
        'limit' => -1,
        'return' => 'ids',
        'date_created' => '>' . date('Y-m-d', strtotime('-7 days'))
    );
    $orders = wc_get_orders($args);
    $sales_by_day = array();
    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        $date = $order->get_date_created()->date('Y-m-d');
        if (!isset($sales_by_day[$date])) {
            $sales_by_day[$date] = 0;
        }
        $sales_by_day[$date] += $order->get_total();
    }
    // Assicuriamoci di avere tutti i 7 giorni, anche se non ci sono vendite
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        if (!isset($sales_by_day[$date])) {
            $sales_by_day[$date] = 0;
        }
    }
    ksort($sales_by_day);
    return $sales_by_day;
}

function ristopos_render_bar_chart($data) {
    echo '<canvas id="bestSellingProductsChart"></canvas>';
    $chart_data = array(
        'labels' => array(),
        'datasets' => array(
            array(
                'label' => 'Quantità Vendute',
                'data' => array(),
                'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1
            )
        )
    );
    foreach ($data as $product) {
        $chart_data['labels'][] = $product['name'];
        $chart_data['datasets'][0]['data'][] = $product['total_quantity'];
    }
    echo '<script>var bestSellingProductsData = ' . json_encode($chart_data) . ';</script>';
}

function ristopos_render_line_chart($data) {
    echo '<canvas id="salesByDayChart"></canvas>';
    $chart_data = array(
        'labels' => array(),
        'datasets' => array(
            array(
                'label' => 'Vendite Totali',
                'data' => array(),
                'fill' => false,
                'borderColor' => 'rgb(75, 192, 192)',
                'tension' => 0.1
            )
        )
    );
    foreach ($data as $date => $total) {
        $chart_data['labels'][] = date('d/m/Y', strtotime($date));
        $chart_data['datasets'][0]['data'][] = $total;
    }
    echo '<script>var salesByDayData = ' . json_encode($chart_data) . ';</script>';
}

