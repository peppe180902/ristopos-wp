<?php
if (!defined('ABSPATH')) {
    exit; // Uscita se accesso diretto
}

function ristopos_get_restaurant_products() {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    return wc_get_products($args);
}

function ristopos_display_products() {
    do_action('ristopos_before_page_content');

    // CATEGORIE PRODOTTI FILTRO
    $products = ristopos_get_restaurant_products();
    $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);


    echo '<div class="wrap ristopos-container">';

echo '<div class="ristopos-tables-section">';
echo '<h2>Seleziona un tavolo</h2>';
echo '<div id="selected-table-info">Nessun tavolo selezionato</div>';
echo '<div class="ristopos-tables-grid">';

$tables = get_option('ristopos_tables', array());
ksort($tables);

foreach ($tables as $table_id => $table_info) {
    $status_class = $table_info['status'] == 'occupied' ? 'table-occupied' : 'table-free';
    $status_text = $table_info['status'] == 'occupied' ? 'Occupato' : 'Libero';
    echo "<div class='ristopos-table $status_class' data-table-id='$table_id'>";
    echo "<span class='table-number'>$table_id</span>";
    echo "<div class='div-table'>";
    echo "<span class='table-status'>" . $status_text . "</span>";
    echo "<span class='table-total'>€" . number_format($table_info['total'], 2) . "</span>";
    echo "</div>";
    echo "</div>";
}

echo '</div>'; // Chiude ristopos-tables-grid
echo '</div>'; // Chiude ristopos-tables-section
        

    echo '<div class="ristopos-main">';
    echo '<h1>Prodotti del Ristorante</h1>';

    // Aggiunta del filtro categorie
    echo '<div class="ristopos-categories-filter">';
    echo '<select id="category-filter">';
    echo '<option value="">Tutte le categorie</option>';
    foreach ($categories as $category) {
        echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    //PRODOTTI
    echo '<h1>Prodotti del Ristorante</h1>';
    echo '<div class="ristopos-products-grid">';
    

    foreach ($products as $product) {
        $product_categories = $product->get_category_ids();
        $category_slugs = array_map(function($cat_id) {
        $term = get_term($cat_id, 'product_cat');
            return $term ? $term->slug : '';
        }, $product_categories);

        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'thumbnail');
        $image_url = $image ? $image[0] : wc_placeholder_img_src('thumbnail');
        
        echo '<div class="ristopos-product" data-categories="' . esc_attr(implode(' ', $category_slugs)) . '">';
        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '">';
        echo '<h3>' . esc_html($product->get_name()) . '</h3>';
        echo '<p class="price">' . wc_price($product->get_price()) . '</p>';
        echo '<button class="button add-to-order" data-product-id="' . esc_attr($product->get_id()) . '" data-product-name="' . esc_attr($product->get_name()) . '" data-product-price="' . esc_attr($product->get_price()) . '">Aggiungi</button>';
        echo '</div>';
    }

    echo '</div></div>';

    // Carrello per desktop
    echo '<div class="ristopos-sidebar ristopos-desktop-cart">';
    echo '<h2>Carrello</h2>';
    echo '<div id="ristopos-cart"></div>';
    echo '<button id="ristopos-checkout" class="button button-primary">Completa l\'ordine</button>';
    echo '<div id="ristopos-message"></div>';
    echo '</div>';

    // Carrello per mobile
    echo '<div class="ristopos-mobile-cart">';
    echo '<div class="ristopos-mobile-cart-content">';
    echo '<button id="ristopos-close-cart" class="ristopos-close-cart">&times;</button>';
    echo '<h2>Carrello</h2>';
    echo '<div id="ristopos-mobile-cart"></div>';
    echo '<button id="ristopos-mobile-checkout" class="button button-primary">Completa l\'ordine</button>';
    echo '<div id="ristopos-mobile-message"></div>';
    echo '</div>';
    echo '</div>';

    echo '</div>'; // Chiude ristopos-container

    // Pulsante mobile per aprire il carrello
    echo '<button id="ristopos-toggle-cart" class="button"><span class="dashicons dashicons-cart"></span><span class="cart-counter">0</span></button>';

    // Modal per i messaggi
    echo '<div id="ristopos-modal" class="ristopos-modal">';
    echo '<div class="ristopos-modal-content">';
    echo '<span class="ristopos-modal-close">&times;</span>';
    echo '<p id="ristopos-modal-message"></p>';
    echo '</div>';
    echo '</div>';

    // Loader
    echo '<div class="loader"></div>';
}

/* function ristopos_enqueue_scripts() {
    wp_enqueue_style('dashicons');
    wp_enqueue_script('ristopos-js', plugin_dir_url(__FILE__) . 'js/ristopos.js', array('jquery'), '1.0', true);
    wp_localize_script('ristopos-js', 'ristopos_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ristopos-nonce')
    ));
} */

function ristopos_products_styles() {
    echo '
    <style>
    .ristopos-div-button {
        display: flex;
        gap: 5px;
    }
.div-table {
    display: flex;
    flex-direction: column;
    gap: 5px;
    align-items: center;
}

.ristopos-tables-section {
    padding: 8px !important;
    margin-bottom: 20px;
}
.ristopos-tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
}
.ristopos-table {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s;
}
.ristopos-table .table-number {
    font-size: 16px;
    font-weight: bold;
    display: block;
}
.ristopos-table .table-status {
    font-size: 12px;
}
.table-free {
    background-color: #e6ffe6;
}
.table-occupied {
    background-color: #ffe6e6;
}
.ristopos-table.selected {
    border-color: #0073aa;
    box-shadow: 0 0 5px rgba(0,115,170,0.5);
}
@media (max-width: 768px) {
    .ristopos-tables-grid {
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    }
    .ristopos-table {
        padding: 5px;
    }
    .ristopos-table .table-number {
        font-size: 14px;
    }
    .ristopos-table .table-status {
        font-size: 10px;
    }
}

    #add-new-table {
        margin-bottom: 20px;
    }

    #selected-table-info {
        margin-bottom: 10px;
        font-weight: bold;
    }

    .dashicons, .dashicons-before:before {
        font-family: dashicons;
        display: inline-block;
        font-weight: 400;
        font-style: normal;
        padding-top: 10px;
        text-decoration: inherit;
        text-transform: none;
        text-rendering: auto;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        width: 40px;
        height: 40px;
        font-size: 30px;
        vertical-align: top;
        text-align: center;
        transition: color .1s ease-in;
    }

    .ristopos-product-notes {
        margin-top: 10px;
        width: 100%;
    }

    .ristopos-product-notes input {
        width: 100% !important;
        padding: 5px !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        resize: vertical !important;
        display: block !important; 
    }

    .ristopos-cart-item-details {
        flex-direction: column;
    }

    .ristopos-cart-item-name {
        font-weight: bold;
        display: block;
    }

    .ristopos-cart-item-price {
        color: #4CAF50;
        font-size: 0.9em;
    }

    .ristopos-cart-item-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-direction: column;
    }

    .ristopos-cart-item-quantity {
        display: flex;
        align-items: center;
        margin-right: 10px;
    }

    .ristopos-cart-item-quantity button {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #4CAF50;
        padding: 0 5px;
    }

    .ristopos-cart-item-quantity span {
        margin: 0 5px;
    }

    .ristopos-cart-total {
        text-align: right;
        margin-top: 15px;
        font-size: 1.2em;
        color: #4CAF50;
    }

    .ristopos-cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .ristopos-cart-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }


        .ristopos-header {
            background-color: #23282d;
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

        .ristopos-modal-close {
            display: none;
        }

        
        #wpfooter {
            display: none !important;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .ristopos-container {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }

        .ristopos-main {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .ristopos-desktop-cart {
            width: 300px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            overflow-y: auto;
            height: 100vh; 
            position: sticky;
            top: 0;
        }

        .ristopos-mobile-cart {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ffffff;
            z-index: 1000;
            overflow-y: auto;
        }

        .ristopos-mobile-cart-content {
            padding: 20px;
            position: relative;
            margin-top: 20px;
        }
        
        .ristopos-sidebar {
            width: 300px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
        }
        .ristopos-cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .ristopos-close-cart {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
        }
        .ristopos-products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
        }

        .ristopos-product {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .ristopos-product img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            margin-bottom: 6px;
        }
        .ristopos-product h3 {
            margin: 6px 0;
            font-size: 12px;
        }
        .ristopos-product .price {
            font-weight: bold;
            color: #4CAF50;
            margin: 3px 0;
            font-size: 11px;
        }

        .added-to-cart {
            color: #4CAF50;
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
        .add-to-order {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 11px;
        }
        .add-to-order:hover {
            background-color: #45a049;
        }
        #ristopos-cart {
            margin-bottom: 20px;
        }
        #ristopos-cart ul {
            list-style-type: none;
            padding: 0;
        }
        #ristopos-cart li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .remove-from-cart {
            background: none;
            border: none;
            cursor: pointer;
            color: #f44336;
            font-size: 18px;
        }
        #ristopos-checkout {
            width: 100%;
            padding: 12px;
            font-size: 16px;
        }
        #ristopos-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .ristopos-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .ristopos-error {
            background-color: #f2dede;
            color: #a94442;
        }
        #ristopos-toggle-cart {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            width: 60px;
            height: 60px;
            border-radius: 30px;
            font-size: 24px;
            line-height: 60px;
            text-align: center;
            padding: 0;
        }
        .ristopos-categories-filter {
            margin-bottom: 20px;
        }

        #category-filter {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .cart-counter {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            line-height: 20px;
            text-align: center;
        }
        
        @media (max-width: 768px) {

            .ristopos-tables-section {
                padding: 8px !important;
                padding-top: 35px !important;
                margin-bottom: 20px;
            }

            .ristopos-mobile-cart-content {
                padding: 20px;
                position: relative;
                margin-top: 70px;
            }

            .ristopos-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1001; 
            }

            .ristopos-container {
                padding-top: 20px; 
            }

            .ristopos-container {
                 flex-direction: column; /* Torna a column su mobile */
            }
        
            .ristopos-desktop-cart {
                 display: none;
            }
        
            #ristopos-toggle-cart {
                 display: block;
            }
        }

        @media (max-width: 768px) {
            .ristopos-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                max-width: 100%;
                z-index: 1000;
                transform: translateY(100%);
                transition: transform 0.3s ease-in-out;
            }
            .ristopos-sidebar.open {
                transform: translateX(-100%);
            }
            .ristopos-close-cart {
                display: block;
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 24px;
                background: none;
                border: none;
                cursor: pointer;
            }
            #ristopos-toggle-cart {
                display: block;
            }
        }
        .loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loader:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 8px;
            border-radius: 50%;
            border: 6px solid #4CAF50;
            border-color: #4CAF50 transparent #4CAF50 transparent;
            animation: loader 1.2s linear infinite;
        }

        @keyframes loader {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    ';
}

// Visualizza la pagina dei prodotti
ristopos_products_styles();
ristopos_display_products();