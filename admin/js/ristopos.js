jQuery(document).ready(function ($) {

    var cart = [];
    var selectedTableId = null;

    function updateCart() {
        var cartHtml = '<ul class="ristopos-cart-list">';
        var total = 0;
        var itemCount = 0;
        cart.forEach(function (item, index) {
            console.log('Processing item:', item);
            cartHtml += '<li class="ristopos-cart-item">' +
                '<div class="ristopos-cart-item-details">' +
                '<span class="ristopos-cart-item-name">' + item.name + '</span>' +
                '<span class="ristopos-cart-item-price">' + item.price.toFixed(2) + ' €</span>' +
                '<div class="ristopos-product-notes">' +
                '<input class="product-note" placeholder="Note per questo prodotto" data-index="' + index + '" value="' + (item.notes || '') + '">' +
                '</div>' +
                '</div>' +
                '<div class="ristopos-cart-item-actions">' +
                '<div class="ristopos-cart-item-quantity">' +
                '<button class="decrease-quantity" data-index="' + index + '">-</button>' +
                '<span>' + item.quantity + '</span>' +
                '<button class="increase-quantity" data-index="' + index + '">+</button>' +
                '</div>' +
                '<button class="remove-from-cart" data-index="' + index + '"><span class="dashicons dashicons-trash"></span></button>' +
                '</div>' +
                '</li>';
            total += item.price * item.quantity;
            itemCount += item.quantity;
        });
        cartHtml += '</ul>';
        cartHtml += '<div class="ristopos-cart-total"><strong>Totale: ' + total.toFixed(2) + ' €</strong></div>';

        $('#ristopos-cart, #ristopos-mobile-cart').html(cartHtml);
        $('.cart-counter').text(itemCount);

        // Aggiungi event listener per le note
        $('.product-note').on('input', function () {
            console.log('Note input detected');
            var index = $(this).data('index');
            cart[index].notes = $(this).val();
            console.log('Updated notes for item at index', index, ':', $(this).val());
        });

        updateCheckoutButtonState();
    }

    function updateCheckoutButtonState() {
        var isDisabled = cart.length === 0 || selectedTableId === null;
        $('#ristopos-checkout, #ristopos-mobile-checkout').prop('disabled', isDisabled);
    }

    function initializeTableSelection() {
        $('.ristopos-table').click(function () {
            selectedTableId = $(this).data('table-id');
            $('.ristopos-table').removeClass('selected');
            $(this).addClass('selected');
            updateSelectedTableInfo();
            updateCheckoutButtonState();
        });
    }

    function updateSelectedTableInfo() {
        if (selectedTableId) {
            $('#selected-table-info').text('Tavolo selezionato: ' + selectedTableId);
            $('#ristopos-checkout').prop('disabled', false);
        } else {
            $('#selected-table-info').text('Nessun tavolo selezionato');
            $('#ristopos-checkout').prop('disabled', true);
        }
    }

    function updateTablesView(tables) {
        var tablesHtml = '';
        // Ordina le chiavi dell'oggetto tables numericamente
        var tableIds = Object.keys(tables).sort(function (a, b) {
            return parseInt(a) - parseInt(b);
        });

        tableIds.forEach(function (tableId) {
            var table = tables[tableId];
            var statusClass = table.status === 'occupied' ? 'table-occupied' : 'table-free';
            /* var statusText = table.status === 'occupied' ? 'Occupato' : 'Libero'; */
            tablesHtml += '<div class="ristopos-table-card ' + statusClass + '">' +
                '<div class="div-header-card">' +
                '<h3>Tavolo ' + tableId + '</h3>' +
                '<button type="button" class="button button-card show-details" data-table-id="' + tableId + '">Dettagli</button>' +
                '</div>' +
                '<p>Stato: ' + (table.status === 'occupied' ? 'Occupato' : 'Libero') + '</p>' +
                '<p>Totale: €' + parseFloat(table.total).toFixed(2) + '</p>' +
                '<div class="div-button-card">' +
                '<button type="button" class="button button-card clear-table" data-table-id="' + tableId + '">Svuota Tavolo</button>' +
                '<button type="button" class="button button-card delete-table" data-table-id="' + tableId + '">Elimina Tavolo</button>' +
                '</div>' +
                '</div>';
        });

        $('.ristopos-tables-grid').html(tablesHtml);

        // Reinizializza gli event listener
        initializeTableDetails();
    }

    function showErrorMessage(message) {
        // Rimuovi eventuali messaggi esistenti
        $('.error-message').remove();

        // Crea e aggiungi il nuovo messaggio
        var $message = $('<div class="error-message">' + message + '</div>');
        $('body').append($message);

        // Mostra il messaggio con un'animazione
        $message.fadeIn(300);

        // Rimuovi il messaggio dopo 3 secondi
        setTimeout(function () {
            $message.fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }


    function showSuccessMessage(message) {
        // Rimuovi eventuali messaggi esistenti
        $('.success-message').remove();

        // Crea e aggiungi il nuovo messaggio
        var $message = $('<div class="success-message">' + message + '</div>');
        $('body').append($message);

        // Mostra il messaggio con un'animazione
        $message.fadeIn(300);

        // Rimuovi il messaggio dopo 3 secondi
        setTimeout(function () {
            $message.fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }


    function addTable() {
        console.log('addTable function called');
        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ristopos_add_table',
                nonce: ristopos_ajax.nonce
            },
            success: function (response) {
                console.log('Add table response:', response);
                if (response.success) {
                    var tables = response.data.tables;
                    updateTablesView(tables);
                    showSuccessMessage('Tavolo ' + response.data.table_id + ' aggiunto con successo!');
                } else {
                    showErrorMessage('Errore nell\'aggiunta del tavolo: ' + response.data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', jqXHR.responseText, textStatus, errorThrown);
                showErrorMessage('Errore nella comunicazione con il server: ' + textStatus);
            }
        });
    }

    function clearTable(tableId) {
        console.log('clearTable function called', tableId);
        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ristopos_clear_table',
                nonce: ristopos_ajax.nonce,
                table_id: tableId
            },
            success: function (response) {
                console.log('Clear table response:', response);
                if (response.success) {
                    var tables = response.data.tables;
                    updateTablesView(tables);
                    showSuccessMessage('Tavolo ' + response.data.table_id + ' svuotato con successo!');
                } else {
                    showErrorMessage('Errore nello svuotamento del tavolo: ' + response.data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', jqXHR.responseText, textStatus, errorThrown);
                showErrorMessage('Errore nella comunicazione con il server: ' + textStatus);
            }
        });
    }

    function deleteTable(tableId) {
        console.log('deleteTable function called', tableId);
        if (confirm('Sei sicuro di voler eliminare questo tavolo?')) {
            $.ajax({
                url: ristopos_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ristopos_delete_table',
                    nonce: ristopos_ajax.nonce,
                    table_id: tableId
                },
                success: function (response) {
                    console.log('Delete table response:', response);
                    if (response.success) {
                        var tables = response.data.tables;
                        updateTablesView(tables);
                        showSuccessMessage('Tavolo ' + response.data.table_id + ' eliminato con successo!');
                    } else {
                        showErrorMessage('Errore nell\'eliminazione del tavolo: ' + response.data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', jqXHR.responseText, textStatus, errorThrown);
                    showErrorMessage('Errore nella comunicazione con il server: ' + textStatus);
                }
            });
        }
    }

    function initializeTableDetails() {
        $(".show-details").on("click", function (e) {
            e.preventDefault();
            var tableId = $(this).data("table-id");
            var $popup = $("#table-details-popup");

            $.ajax({
                url: ristopos_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "ristopos_get_table_details",
                    nonce: ristopos_ajax.nonce,
                    table_id: tableId
                },
                success: function (response) {
                    if (response.success) {
                        var orders = response.data.orders;
                        var detailsHtml = "<h4>Dettagli Tavolo " + tableId + "</h4>";
                        detailsHtml += "<span class='close-popup'>×</span>";

                        orders.forEach(function (order, index) {
                            detailsHtml += "<h5>Ordine " + (index + 1) + " (ID: " + order.order_id + ")</h5>";
                            detailsHtml += "<p>Cameriere: " + order.waiter + "</p>";
                            detailsHtml += "<p>Data: " + order.date + "</p>";
                            detailsHtml += "<ul>";
                            order.products.forEach(function (product) {
                                detailsHtml += "<li>" + product.name + " (x" + product.quantity + ")";
                                if (product.note) {
                                    detailsHtml += "<br><small>Nota: " + product.note + "</small>";
                                }
                                detailsHtml += "</li>";
                            });
                            detailsHtml += "</ul>";
                        });

                        $popup.html(detailsHtml);

                        // Posiziona il popup al centro dello schermo
                        $popup.css({
                            'position': 'fixed',
                            'top': '50%',
                            'left': '50%',
                            'transform': 'translate(-50%, -50%)'
                        }).show();

                        // Aggiungi l'evento di chiusura
                        $popup.find('.close-popup').on('click', function () {
                            $popup.hide();
                        });
                    } else {
                        showErrorMessage("Errore nel caricamento dei dettagli: " + response.data.message);
                    }
                },
                error: function () {
                    showErrorMessage("Errore nella comunicazione con il server");
                }
            });
        });

        // Chiudi il popup quando si clicca fuori da esso
        $(document).on("click", function (e) {
            var $popup = $("#table-details-popup");
            if (!$(e.target).closest(".table-details-popup").length &&
                !$(e.target).hasClass("show-details") &&
                $popup.is(":visible")) {
                $popup.hide();
            }
        });
    }


    // Assicurati di chiamare questa funzione quando il documento è pronto
    $(document).ready(function () {
        initializeTableDetails();
    });

    $(document).on('click', '#add-table', function (e) {
        console.log('Add table clicked');
        e.preventDefault();
        addTable();
    });

    $(document).on('click', '.delete-table', function (e) {
        console.log('Delete table clicked');
        e.preventDefault();
        var tableId = $(this).data('table-id');
        if (confirm('Sei sicuro di voler eliminare questo tavolo?')) {
            deleteTable(tableId);
        }
    });

    $(document).on('click', '.clear-table', function (e) {
        console.log('Clear table clicked');
        e.preventDefault();
        var tableId = $(this).data('table-id');
        clearTable(tableId);
    });

    // Chiamata immediata per forzare l'aggiornamento del carrello
    $(document).ready(function () {
        updateCart();
        initializeTableSelection();
        updateSelectedTableInfo();
    });

    $(document).on('click', '.increase-quantity', function () {
        var index = $(this).data('index');
        cart[index].quantity++;
        updateCart();
    });

    $(document).on('click', '.decrease-quantity', function () {
        var index = $(this).data('index');
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
        } else {
            cart.splice(index, 1);
        }
        updateCart();
    });

    function showModal(message) {
        $('#ristopos-modal-message').text(message);
        $('#ristopos-modal').show();
    }

    function showAddedToCartMessage($button) {
        var $message = $('<div class="added-to-cart">Aggiunto al carrello</div>');
        $button.closest('.ristopos-product').append($message);
        setTimeout(function () {
            $message.fadeOut(function () {
                $(this).remove();
            });
        }, 3000);
    }

    $('.add-to-order').click(function () {
        console.log('Add to order clicked');
        var $button = $(this);
        var productId = $button.data('product-id');
        var productName = $button.data('product-name');
        var productPrice = parseFloat($button.data('product-price'));

        console.log('Product details:', { id: productId, name: productName, price: productPrice });

        var existingItem = cart.find(item => item.id === productId);
        if (existingItem) {
            console.log('Existing item found, increasing quantity');
            existingItem.quantity += 1;
        } else {
            console.log('New item, adding to cart');
            cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1,
                notes: ''
            });
        }

        console.log('Updated cart:', JSON.stringify(cart, null, 2));

        updateCart();
        showAddedToCartMessage($button);
    });


    $('#category-filter').on('change', function () {
        var selectedCategory = $(this).val();

        if (selectedCategory === '') {
            $('.ristopos-product').show();
        } else {
            $('.ristopos-product').hide();
            $('.ristopos-product[data-categories*="' + selectedCategory + '"]').show();
        }
    });

    $(document).on('click', '.remove-from-cart', function () {
        var index = $(this).data('index');
        cart.splice(index, 1);
        updateCart();
    });

    function checkoutProcess() {
        if (cart.length === 0) {
            showModal('Il carrello è vuoto');
            return;
        }

        if (selectedTableId === null) {
            showModal('Seleziona un tavolo prima di completare l\'ordine');
            return;
        }

        $('.loader').css('display', 'flex');

        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ristopos_create_order',
                nonce: ristopos_ajax.nonce,
                cart: JSON.stringify(cart),
                table_id: selectedTableId
            },
            success: function (response) {
                $('.loader').hide();
                if (response.success) {
                    showModal('Ordine creato con successo!');
                    $('#ristopos-message, #ristopos-mobile-message').html('<div class="ristopos-success">Ordine completato con successo!</div>');

                    // Aggiorna lo stato e il totale del tavolo
                    var $selectedTable = $('.ristopos-table[data-table-id="' + selectedTableId + '"]');
                    $selectedTable.removeClass('table-free').addClass('table-occupied');
                    $selectedTable.find('.table-status').text('Occupied');
                    $selectedTable.find('.table-total').text('€' + response.data.table_total.toFixed(2));

                    cart = [];
                    updateCart();
                    updateSelectedTableInfo();
                } else {
                    showModal('Errore nella creazione dell\'ordine: ' + response.data);
                    $('#ristopos-message, #ristopos-mobile-message').html('<div class="ristopos-error">Errore nella creazione dell\'ordine.</div>');
                }
            },
            error: function () {
                $('.loader').hide();
                showModal('Errore nella comunicazione con il server');
                $('#ristopos-message, #ristopos-mobile-message').html('<div class="ristopos-error">Errore nella comunicazione con il server.</div>');
            }
        });
    }


    function loadProducts() {
        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ristopos_get_products',
                nonce: ristopos_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    displayProducts(response.data);
                } else {
                    showErrorMessage('Errore nel caricamento dei prodotti');
                }
            },
            error: function () {
                showErrorMessage('Errore nella comunicazione con il server');
            }
        });
    }

    function displayProducts(products) {
        var productList = $('#product-list');
        productList.empty();
        
        products.forEach(function(product) {
            var productHtml = '<div class="product-item">' +
                '<img src="' + product.image + '" alt="' + product.name + '">' +
                '<h3>' + product.name + '</h3>' +
                '<p>Prezzo: ' + product.price + ' €</p>' +
                '<p>Categorie: ' + product.categories.join(', ') + '</p>' +
                '<div class="div-button-product">' +
                '<button class="edit-product button" data-id="' + product.id + '">Modifica</button>' +
                '<button class="delete-product button" data-id="' + product.id + '">Elimina</button>' +
                '</div>' +
                '</div>';
            productList.append(productHtml);
        });
    }

    $('#ristopos-add-product-form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'ristopos_add_product');
        formData.append('nonce', ristopos_ajax.nonce);

        // Mostra il loader
        $('#loader').show();

        // Disabilita il pulsante di submit
        $(this).find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                // Nascondi il loader
                $('#loader').hide();

                // Riabilita il pulsante di submit
                $('#ristopos-add-product-form').find('button[type="submit"]').prop('disabled', false);

                if (response.success) {
                    showSuccessMessage('Prodotto aggiunto con successo');
                    $('#ristopos-add-product-form')[0].reset();
                    loadProducts();
                } else {
                    showErrorMessage('Errore nell\'aggiunta del prodotto: ' + response.data);
                }
            },
            error: function () {
                // Nascondi il loader
                $('#loader').hide();

                // Riabilita il pulsante di submit
                $('#ristopos-add-product-form').find('button[type="submit"]').prop('disabled', false);
                showErrorMessage('Errore nella comunicazione con il server');
            }
        });
    });

    $(document).on('click', '.edit-product', function () {
        var productId = $(this).data('id');
        var productItem = $(this).closest('.product-item');
        var productName = productItem.find('h3').text();
        var productPrice = productItem.find('p:contains("Prezzo:")').text().split(':')[1].trim().replace(' €', '');
        var productCategories = productItem.find('p:contains("Categorie:")').text().split(':')[1].trim().split(', ');

        $('#ristopos-edit-product-form input[name="product_id"]').val(productId);
        $('#ristopos-edit-product-form input[name="product_name"]').val(productName);
        $('#ristopos-edit-product-form input[name="product_price"]').val(productPrice);
        $('#ristopos-edit-product-form select[name="product_category[]"]').val(productCategories);

        $('#edit-product-modal').show();
    });

    $(document).on('click', '.delete-product', function() {
        var productId = $(this).data('id');
        if (confirm('Sei sicuro di voler eliminare questo prodotto?')) {
            $.ajax({
                url: ristopos_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ristopos_delete_product',
                    nonce: ristopos_ajax.nonce,
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        showSuccessMessage('Prodotto eliminato con successo');
                        loadProducts(); // Ricarica la lista dei prodotti
                    } else {
                        showErrorMessage('Errore nell\'eliminazione del prodotto: ' + response.data);
                    }
                },
                error: function() {
                    showErrorMessage('Errore nella comunicazione con il server');
                }
            });
        }
    });

    $('.ristopos-modal-close').click(function () {
        $('#edit-product-modal').hide();
    });

    $(window).click(function (event) {
        if ($(event.target).hasClass('ristopos-modal')) {
            $('#edit-product-modal').hide();
        }
    });

    $('#ristopos-edit-product-form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'ristopos_update_product');
        formData.append('nonce', ristopos_ajax.nonce);

        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    showSuccessMessage('Prodotto aggiornato con successo');
                    $('#edit-product-modal').hide();
                    loadProducts();
                } else {
                    showErrorMessage('Errore nell\'aggiornamento del prodotto: ' + response.data);
                }
            },
            error: function () {
                showErrorMessage('Errore nella comunicazione con il server');
            }
        });
    });

    $(document).ready(function () {
        if ($('#product-list').length) {
            loadProducts();
        }
    });

    // Funzione per aggiornare la lista dei prodotti nel POS
    function updatePOSProductList() {
        if ($('.ristopos-products-grid').length) {
            loadProducts();
        }
    }

    // Aggiorna la funzione loadProducts per gestire sia la pagina di gestione prodotti che il POS
    function loadProducts() {
        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ristopos_get_products',
                nonce: ristopos_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    if ($('#product-list').length) {
                        displayProducts(response.data);
                    } else if ($('.ristopos-products-grid').length) {
                        displayPOSProducts(response.data);
                    }
                } else {
                    showErrorMessage('Errore nel caricamento dei prodotti');
                }
            },
            error: function () {
                showErrorMessage('Errore nella comunicazione con il server');
            }
        });
    }

    // Funzione per visualizzare i prodotti nel POS
    function displayPOSProducts(products) {
        var $productsGrid = $('.ristopos-products-grid');
        $productsGrid.empty();

        products.forEach(function (product) {
            var productHtml = '<div class="ristopos-product" data-categories="' + product.categories.join(' ') + '">' +
                '<img src="' + product.image + '" alt="' + product.name + '">' +
                '<h3>' + product.name + '</h3>' +
                '<p class="price">' + product.price + ' €</p>' +
                '<button class="button add-to-order" data-product-id="' + product.id + '" ' +
                'data-product-name="' + product.name + '" ' +
                'data-product-price="' + product.price + '">Aggiungi</button>' +
                '</div>';
            $productsGrid.append(productHtml);
        });
    }

    $('#ristopos-checkout, #ristopos-mobile-checkout').click(checkoutProcess);

    $('#ristopos-toggle-cart').click(function () {
        $('.ristopos-mobile-cart').show();
    });

    $('#ristopos-close-cart').click(function () {
        $('.ristopos-mobile-cart').hide();
    });

    $('.ristopos-modal-close').click(function () {
        $('#ristopos-modal').hide();
    });

    $(window).click(function (event) {
        if ($(event.target).hasClass('ristopos-modal')) {
            $('#ristopos-modal').hide();
        }
    });

    // Inizializzazione del contatore del carrello
    updateCart();
});