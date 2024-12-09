<?php
if (!defined('ABSPATH')) {
    exit; // Uscita se accesso diretto
}

function ristopos_display_tables() {
    do_action('ristopos_before_page_content');

    $tables = get_option('ristopos_tables', array());
    ksort($tables);

    echo '<div class="wrap">';
    echo '<h1>Gestione Tavoli RistoPOS</h1>';

    echo '<button type="button" id="add-table" class="button button-card">Aggiungi Nuovo Tavolo</button>';

    echo '<div class="ristopos-tables-grid">';
    foreach ($tables as $table_id => $table_info) {
        $status_class = $table_info['status'] == 'occupied' ? 'table-occupied' : 'table-free';
        $status_text = $table_info['status'] == 'occupied' ? 'Occupato' : 'Libero';
        echo "<div class='ristopos-table-card $status_class'>";

        echo '<div class="div-header-card">';
        echo "<h3>Tavolo $table_id</h3>";
        echo '<button type="button" class="button button-card show-details" data-table-id="' . $table_id . '">Dettagli</button>';
        echo "</div>";
        
        echo "<p>Stato: $status_text</p>";
        echo "<p>Totale: €" . number_format($table_info['total'], 2) . "</p>";
        
        echo '<div class="div-button-card">';
        echo '<button type="button" class="button button-card clear-table" data-table-id="' . $table_id . '">Svuota Tavolo</button>';
        echo '<button type="button" class="button button-card delete-table" data-table-id="' . $table_id . '">Elimina Tavolo</button>';
        echo "</div>";

        echo "</div>";
    }
    echo '</div>';

    echo '<div id="table-details-popup" class="table-details-popup"></div>';

    echo '</div>';

    ristopos_tables_styles();
}

function ristopos_tables_styles() {
    echo '
    <style>

.table-details-popup {
    display: none;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 1000;
    max-width: 90%;
    width: 300px;
    max-height: 80vh;
    overflow-y: auto;
}

    .table-details-popup h4 {
        margin-top: 0;
        word-break: break-word;
    }

    .table-details-popup ul {
        padding-left: 20px;
        margin: 0;
        word-break: break-word;
    }

    .table-details-popup .close-popup {
        position: absolute;
        top: 5px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
    }


    @media screen and (max-width: 600px) {
        .table-details-popup {
            width: 90%;
        }
    }

    .button-card:hover {
        box-shadow: -2px -2px;
    }

    .button-card {
        color: #2271b1;
        border-color: #2271b1;
        background: #fff;
        vertical-align: top;
        border-radius: 10px;
        border: none;
        box-shadow: 2px 2px;
    }

    .div-button-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .div-header-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .error-message {
    background-color: #f44336;
    color: white;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    display: none;
}

    .success-message {
       background-color: #4CAF50;
       color: white;
       padding: 15px;
       margin-bottom: 15px;
       border-radius: 4px;
       position: fixed;
       top: 20px;
       right: 20px;
       z-index: 1000;
       display: none;
    }

    .ristopos-table-card form {
        margin-top: 10px;
        display: inline-block;
        margin-right: 5px;
    }

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
    .ristopos-tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .ristopos-table-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .table-occupied {
        background-color: #ffe6e6;
    }
    .table-free {
        background-color: #e6ffe6;
    }
    .add-table-form {
        margin-bottom: 20px;
    }
    .ristopos-table-card h3 {
        margin-top: 0;
    }
    .ristopos-table-card form {
        margin-top: 10px;
    }
         @media screen and (max-width: 600px) {
            // #wpbody {
            //     padding-top: 0px;
            // }
        }
    </style>
    ';
}

function ristopos_add_table_action() {
    $tables = get_option('ristopos_tables', array());
    
    // Troviamo il prossimo ID disponibile
    $new_table_id = 1;
    while (isset($tables[$new_table_id])) {
        $new_table_id++;
    }
    
    $tables[$new_table_id] = array(
        'status' => 'free',
        'total' => 0,
        'orders' => array()
    );
    update_option('ristopos_tables', $tables);
    echo '<div class="updated"><p>Nuovo tavolo (ID: ' . $new_table_id . ') aggiunto con successo!</p></div>';
}

function ristopos_delete_table_action($table_id) {
    $tables = get_option('ristopos_tables', array());
    if (isset($tables[$table_id])) {
        unset($tables[$table_id]);
        update_option('ristopos_tables', $tables);
        echo '<div class="updated"><p>Tavolo ' . $table_id . ' eliminato con successo!</p></div>';
    } else {
        echo '<div class="error"><p>Tavolo ' . $table_id . ' non trovato.</p></div>';
    }
}

function ristopos_clear_table_action($table_id) {
    $tables = get_option('ristopos_tables', array());
    if (isset($tables[$table_id])) {
        $tables[$table_id]['status'] = 'free';
        $tables[$table_id]['total'] = 0;
        $tables[$table_id]['orders'] = array();
        update_option('ristopos_tables', $tables);
        echo '<div class="updated"><p>Tavolo ' . $table_id . ' svuotato con successo!</p></div>';
    }
}

ristopos_display_tables();
ristopos_tables_styles();
