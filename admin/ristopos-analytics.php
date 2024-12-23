<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Check if WooCommerce functions are available.
 *
 * @return bool
 */
function ristopos_wc_functions_available()
{
    return function_exists('WC') && function_exists('wc_price') && function_exists('wc_get_orders');
}

/**
 * Enqueue inline styles for the RistoPOS dashboard.
 */
function ristopos_analytics_styles()
{
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
        .ristopos-stats-grid {
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
    </style>';
}

/**
 * Display main statistics on the dashboard.
 */
function ristopos_display_main_stats()
{
    $total_sales = ristopos_get_total_sales();
    $total_orders = ristopos_get_total_orders();
    $average_order_value = ristopos_get_average_order_value();
    ?>
    <div class="ristopos-stats-grid">
        <div class="ristopos-stat-card">
            <h3><?php esc_html_e('Total Sales', 'ristopos'); ?></h3>
            <p class="ristopos-big-number"><?php echo wc_price($total_sales); ?></p>
        </div>
        <div class="ristopos-stat-card">
            <h3><?php esc_html_e('Total Orders', 'ristopos'); ?></h3>
            <p class="ristopos-big-number"><?php echo esc_attr( $total_orders ); ?></p>
        </div>
        <div class="ristopos-stat-card">
            <h3><?php esc_html_e('Average Order Value', 'ristopos'); ?></h3>
            <p class="ristopos-big-number"><?php echo wc_price($average_order_value); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Get the total sales from WooCommerce orders.
 *
 * @return float
 */
function ristopos_get_total_sales()
{
    $args = [
        'status' => 'completed',
        'limit' => -1,
        'return' => 'ids',
    ];
    $orders = wc_get_orders($args);
    $total_sales = 0;

    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        $total_sales += $order->get_total();
    }

    return $total_sales;
}

/**
 * Get the total number of completed orders.
 *
 * @return int
 */
function ristopos_get_total_orders()
{
    $args = [
        'status' => 'completed',
        'return' => 'ids',
        'limit' => -1,
    ];
    $orders = wc_get_orders($args);

    return count($orders);
}

/**
 * Calculate the average order value.
 *
 * @return float
 */
function ristopos_get_average_order_value()
{
    $total_sales = ristopos_get_total_sales();
    $total_orders = ristopos_get_total_orders();

    return $total_orders > 0 ? $total_sales / $total_orders : 0;
}

/**
 * Display charts on the dashboard.
 */
function ristopos_display_charts()
{
    $best_selling_products = ristopos_get_best_selling_products();
    $sales_by_day = ristopos_get_sales_by_day();

    ?>
    <div class="ristopos-charts-grid">
        <div class="ristopos-chart-card">
            <h3><?php esc_html_e('Best Selling Products', 'ristopos'); ?></h3>
            <?php ristopos_render_bar_chart($best_selling_products); ?>
        </div>
        <div class="ristopos-chart-card">
            <h3><?php esc_html_e('Sales by Day', 'ristopos'); ?></h3>
            <?php ristopos_render_line_chart($sales_by_day); ?>
        </div>
    </div>
    <?php
}

/**
 * Get the best-selling products.
 *
 * @return array
 */
function ristopos_get_best_selling_products()
{
    $args = [
        'post_type' => 'product',
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 5,
        'order' => 'DESC',
    ];
    $products = new WP_Query($args);
    $best_selling = [];

    while ($products->have_posts()) {
        $products->the_post();
        $product = wc_get_product(get_the_ID());
        $best_selling[] = [
            'name' => $product->get_name(),
            'total_quantity' => $product->get_total_sales(),
        ];
    }

    wp_reset_postdata();
    return $best_selling;
}

/**
 * Get sales data by day for the last 7 days.
 *
 * @return array
 */
function ristopos_get_sales_by_day()
{
    $args = [
        'status' => 'completed',
        'limit' => -1,
        'return' => 'ids',
        'date_created' => '>' . date('Y-m-d', strtotime('-7 days')),
    ];
    $orders = wc_get_orders($args);
    $sales_by_day = [];

    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        $date = $order->get_date_created()->date('Y-m-d');
        if (!isset($sales_by_day[$date])) {
            $sales_by_day[$date] = 0;
        }
        $sales_by_day[$date] += $order->get_total();
    }

    // Ensure all 7 days are present.
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("-$i days"));
        if (!isset($sales_by_day[$date])) {
            $sales_by_day[$date] = 0;
        }
    }

    ksort($sales_by_day);
    return $sales_by_day;
}

/**
 * Render a bar chart for the best-selling products.
 *
 * @param array $data Best-selling products data.
 */
function ristopos_render_bar_chart($data)
{
    echo '<canvas id="bestSellingProductsChart"></canvas>';
    $chart_data = [
        'labels' => [],
        'datasets' => [
            [
                'label' => __('Units Sold', 'ristopos'),
                'data' => [],
                'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1,
            ],
        ],
    ];

    foreach ($data as $product) {
        $chart_data['labels'][] = $product['name'];
        $chart_data['datasets'][0]['data'][] = $product['total_quantity'];
    }

    echo '<script>var bestSellingProductsData = ' . wp_json_encode($chart_data) . ';</script>';
}

/**
 * Render a line chart for sales by day.
 *
 * @param array $data Sales data by day.
 */
function ristopos_render_line_chart($data)
{
    echo '<canvas id="salesByDayChart"></canvas>';
    $chart_data = [
        'labels' => [],
        'datasets' => [
            [
                'label' => __('Total Sales', 'ristopos'),
                'data' => [],
                'fill' => false,
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'tension' => 0.1,
            ],
        ],
    ];

    foreach ($data as $date => $sales) {
        $chart_data['labels'][] = $date;
        $chart_data['datasets'][0]['data'][] = $sales;
    }

    echo '<script>var salesByDayData = ' . wp_json_encode($chart_data) . ';</script>';
}
