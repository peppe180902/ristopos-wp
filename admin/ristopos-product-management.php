<?php
if (!defined('ABSPATH')) {
    exit; // Uscita se accesso diretto
}

function ristopos_display_product_management() {
    do_action('ristopos_before_page_content');
    
    echo '<div class="wrap ristopos-product-management">';
    echo '<h1 id="title-page" >Gestione Prodotti RistoPOS</h1>';
    
    echo '<div id="add-product-form" class="ristopos-form">';
    echo '<h2>Aggiungi Nuovo Prodotto</h2>';
    echo '<form id="ristopos-add-product-form">';
    echo '<input type="text" name="product_name" placeholder="Nome Prodotto" required>';
    echo '<input type="number" step="0.01" name="product_price" placeholder="Prezzo" required>';
    echo '<label for="product_category">Seleziona Categoria/e:</label>';
    echo '<select name="product_category[]" id="product_category" multiple>';
    $categories = get_terms('product_cat', array('hide_empty' => false));
    foreach ($categories as $category) {
        echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
    }
    echo '</select>';
    echo '<input type="file" name="product_image">';
    echo '<button type="submit" class="button button-primary">Aggiungi Prodotto</button>';
    echo '</form>';
    echo '</div>';

    echo '<div id="product-list">';
    // Il contenuto della lista prodotti sarà caricato via AJAX
    echo '</div>';

    echo '<div id="edit-product-modal" class="ristopos-modal">';
    echo '<div class="ristopos-modal-content">';
    echo '<span class="ristopos-modal-close">&times;</span>';
    echo '<h2>Modifica Prodotto</h2>';
    echo '<form id="ristopos-edit-product-form">';
    echo '<input type="hidden" name="product_id">';
    echo '<input type="text" name="product_name" placeholder="Nome Prodotto" required>';
    echo '<input type="number" step="0.01" name="product_price" placeholder="Prezzo" required>';
    echo '<select name="product_category[]" multiple>';
    foreach ($categories as $category) {
        echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
    }
    echo '</select>';
    echo '<input type="file" name="product_image">';
    echo '<button type="submit" class="button button-primary">Aggiorna Prodotto</button>';
    echo '</form>';
    echo '<div id="loader" class="loader" style="display: none;"></div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
}

function ristopos_product_management_styles() {
    echo '
    <style>
    .div-button-product {
        display: flex;
        justify-content: space-evenly;
    }
    .delete-product {
        background-color: #dc3545;
        color: white;
        margin-left: 5px;
    }

    .delete-product:hover {
        background-color: #c82333;
    }
    .loader {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    label[for="product_category"] {
        display: block;
        margin-top: 10px;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .wrap {
        margin: 10px 20px 0 20px;
    }
    .ristopos-div-button {
        display: flex;
        gap: 5px;
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
    .ristopos-product-management {
        margin-top: 20px;
    }
    .ristopos-form {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .ristopos-form input, .ristopos-form select {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .ristopos-form button {
        width: 100%;
        padding: 10px;
    }
    #product-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }
    .product-item {
        background: #fff;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
        text-align: center;
    }
    .product-item img {
        width: 150px;
        height: 150px:
        margin-bottom: 10px;
    }
    .ristopos-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    .ristopos-modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 5px;
    }
    .ristopos-modal-close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .ristopos-modal-close:hover,
    .ristopos-modal-close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }   
    @media (max-width: 768px) {
        .div-button-product {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        #title-page {
            padding-top: 50px;
        }
         .ristopos-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001; 
        }
        #product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 20px;
        }
    }
    </style>
    ';
}

ristopos_display_product_management();
ristopos_product_management_styles();
