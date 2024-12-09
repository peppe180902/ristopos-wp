<?php
// ristopos-navigation.php

function ristopos_generate_navigation() {
    ob_start();
    ?>
    <style>
    /* #ristopos-nav-wrapper {
        background: #23282d;
        padding: 10px;
        position: relative;
    }

    #ristopos-menu-toggle {
        background: transparent;
        color: #fff;
        border: none;
        padding: 10px;
        cursor: pointer;
    }

    #ristopos-sidebar {
        position: fixed;
        top: 0px; /* Adjust based on your admin bar height */
        left: -250px;
        width: 250px;
        height: 100%;
        background: #32373c;
        transition: all 0.3s ease-in-out;
        opacity: 0;
        z-index: 9999;
        overflow-y: auto;
    }

    #ristopos-sidebar.open {
        left: 0;
        opacity: 1;
    }

    #ristopos-sidebar ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    #ristopos-sidebar li a {
        display: block;
        padding: 15px 20px;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    #ristopos-sidebar li a:hover {
        background-color: #191e23;
    }

    #ristopos-sidebar .dashicons {
        margin-right: 10px;
    }

    @media screen and (max-width: 782px) {
        #ristopos-sidebar {
            top: 46px; /* Adjust for mobile admin bar */
            height: calc(100% - 46px);
        }
        #ristopos-sidebar li a {
            padding: 10px 15px;
        }
    } */
    </style>

    <div id="ristopos-nav-wrapper">
        <button id="ristopos-menu-toggle" aria-label="Toggle menu">
            <span class="dashicons dashicons-menu"></span>
        </button>
        <nav id="ristopos-sidebar">
            <ul>
                <li><a href="<?php echo admin_url('admin.php?page=ristopos'); ?>"><span class="dashicons dashicons-dashboard"></span> Dashboard</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=ristopos-orders'); ?>"><span class="dashicons dashicons-cart"></span> Ordini</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=ristopos-tables'); ?>"><span class="dashicons dashicons-grid-view"></span> Tavoli</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=ristopos-product-management'); ?>"><span class="dashicons dashicons-products"></span> Prodotti</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=ristopos-products'); ?>"><span class="dashicons dashicons-money-alt"></span> POS</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=ristopos-analytics'); ?>"><span class="dashicons dashicons-chart-bar"></span> Reportistica</a></li>
                <li><a href="<?php echo admin_url('admin.php?page=ristopos-staff'); ?>"><span class="dashicons dashicons-groups"></span> Personale</a></li>
                <li class="wp-return"><a href="<?php echo admin_url(); ?>"><span class="dashicons dashicons-wordpress"></span> Torna a WordPress</a></li>
                </ul>
        </nav>
    </div>

    <script>
    // jQuery(document).ready(function($) {
    //     console.log('RistoPOS navigation script loaded');

    //     var $menuToggle = $('#ristopos-menu-toggle');
    //     var $sidebar = $('#ristopos-sidebar');
    //     var $body = $('body');

    //     console.log('Menu toggle button:', $menuToggle.length);
    //     console.log('Sidebar:', $sidebar.length);

    //     $menuToggle.on('click', function(e) {
    //         e.preventDefault();
    //         $sidebar.toggleClass('open');
    //         $body.toggleClass('sidebar-open');
    //         console.log('Sidebar toggled');
    //     });

    //     $(document).on('click', function(event) {
    //         if (!$(event.target).closest('#ristopos-nav-wrapper, #ristopos-sidebar').length) {
    //             $sidebar.removeClass('open');
    //             $body.removeClass('sidebar-open');
    //         }
    //     });

    //     $sidebar.on('click', 'a', function() {
    //         $sidebar.removeClass('open');
    //         $body.removeClass('sidebar-open');
    //     });
    // });
    </script>
    <?php
    return ob_get_clean();
}

// Assicurati che questa funzione sia chiamata nel posto giusto
function ristopos_add_navigation() {
    echo ristopos_generate_navigation();
}

// Aggiungi questa funzione all'inizio di ogni pagina del plugin
// add_action('ristopos_before_page_content', 'ristopos_add_navigation');