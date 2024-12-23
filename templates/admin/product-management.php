<?php
function ristopos_display_product_management() {
    do_action('ristopos_before_page_content');
    
    // Main container with modern layout
    echo '<div class="wrap ristopos-product-management">';
    echo '<header class="ristopos-header">';
    echo '<h1 class="page-title">' . esc_html__('RistoPOS Product Management', 'ristopos') . '</h1>';
    echo '</header>';
    
    // Product form with improved styling 
    echo '<div class="ristopos-card ristopos-form">';
    echo '<div class="card-header">';
    echo '<h2>' . esc_html__('Add New Product', 'ristopos') . '</h2>';
    echo '</div>';
    
    echo '<form id="ristopos-add-product-form" class="product-form">';
    echo '<div class="form-group">';
    echo '<label for="product_name">' . esc_html__('Product Name', 'ristopos') . '</label>';
    echo '<input type="text" id="product_name" name="product_name" required>';
    echo '</div>';

    echo '<div class="form-group">'; 
    echo '<label for="product_price">' . esc_html__('Price', 'ristopos') . '</label>';
    echo '<input type="number" id="product_price" step="0.01" name="product_price" required>';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="product_category">' . esc_html__('Categories', 'ristopos') . '</label>';
    echo '<select name="product_category[]" id="product_category" multiple class="category-select">';
    $categories = get_terms('product_cat', array('hide_empty' => false));
    foreach ($categories as $category) {
        echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    echo '<div class="form-group">';
    echo '<label for="product_image" class="file-upload-label">';
    echo '<span class="dashicons dashicons-upload"></span> ' . esc_html__('Upload Image', 'ristopos');
    echo '</label>';
    echo '<input type="file" id="product_image" name="product_image" class="file-upload-input">';
    echo '</div>';

    echo '<button type="submit" class="button button-primary submit-button">';
    echo '<span class="dashicons dashicons-plus-alt"></span> ';
    echo esc_html__('Add Product', 'ristopos');
    echo '</button>';
    echo '</form>';
    echo '</div>';

    // Products grid
    echo '<div id="product-list" class="products-grid">';
    echo '</div>';

    // Modal styling improved
    echo '<div id="edit-product-modal" class="ristopos-modal">'; 
    echo '<div class="modal-content">';
    echo '<header class="modal-header">';
    echo '<h2>' . esc_html__('Edit Product', 'ristopos') . '</h2>';
    echo '<button class="modal-close">&times;</button>';
    echo '</header>';

    // Modal form with same styling as add form
    echo '<form id="ristopos-edit-product-form" class="product-form">...</form>';
    echo '</div>';
    echo '</div>';

    echo '</div>';
}

// Add modern styles
function ristopos_product_management_styles() {
    echo '
    <style>
    .ristopos-product-management {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .ristopos-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 600;
        color: #1e1e1e;
    }

    .ristopos-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .card-header {
        margin-bottom: 1.5rem;
    }

    .product-form {
        display: grid;
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: #1e1e1e;
    }

    .form-group input,
    .form-group select {
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #2271b1;
        box-shadow: 0 0 0 1px #2271b1;
        outline: none;
    }

    .file-upload-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: #f0f0f1;
        border-radius: 4px;
        cursor: pointer;
    }

    .file-upload-input {
        display: none;
    }

    .submit-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    #product-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }
    
    .product-item {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .product-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .product-item img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .product-item h3 {
        margin: 0 0 10px 0;
        font-size: 1.1rem;
        color: #333;
    }

    .product-item .price {
        font-weight: 600;
        color: #2271b1;
        margin-bottom: 15px;
    }

    .product-item .actions {
        margin-top: auto;
        display: flex;
        gap: 10px;
    }

    .div-button-product {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-item .edit-btn,
    .product-item .delete-btn {
        padding: 8px 12px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        flex: 1;
        transition: background-color 0.2s;
    }

    .product-item .edit-btn {
        background: #2271b1;
        color: white;
    }

    .product-item .delete-btn {
        background: #dc3545;
        color: white;
    }

    .product-item .edit-btn:hover {
        background: #135e96;
    }

    .product-item .delete-btn:hover {
        background: #bb2d3b;
    }

    @media (max-width: 768px) {
        #product-list {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }
        
        .product-item {
            padding: 15px;
        }
        
        .product-item img {
            height: 140px;
        }
    }

    @media (max-width: 768px) {
        .ristopos-product-management {
            padding: 1rem;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
    </style>
    ';
}

ristopos_display_product_management();
ristopos_product_management_styles();
