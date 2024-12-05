/* jQuery(document).ready(function($) {
    var $menuToggle = $('#ristopos-menu-toggle');
    var $sidebar = $('#ristopos-sidebar');

    $menuToggle.on('click', function(e) {
        e.preventDefault();
        $sidebar.toggleClass('open');
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#ristopos-nav-wrapper').length) {
            $sidebar.removeClass('open');
        }
    });

    // Debug: Aggiungi questo per verificare che lo script sia caricato
    console.log('RistoPOS navigation script loaded');
}); */