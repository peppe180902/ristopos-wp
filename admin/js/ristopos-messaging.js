jQuery(document).ready(function($) {
    console.log('ristopos_ajax object:', ristopos_ajax);
    
    if (typeof ristopos_ajax === 'undefined' || typeof ristopos_ajax.security === 'undefined') {
        console.error('ristopos_ajax or security is undefined');
        return;
    }

    console.log('JavaScript received nonce:', ristopos_ajax.security);

    $('#send-message-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submit intercepted');

        var form = $(this);
        var recipientId = form.find('select[name="recipient_id"]').val();
        var message = form.find('textarea[name="message"]').val();

        console.log('Sending message:', { recipientId: recipientId, message: message });
        console.log('Security nonce:', ristopos_ajax.security);

        $.ajax({
            url: ristopos_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'ristopos_send_message',
                security: ristopos_ajax.security,
                recipient_id: recipientId,
                message: message
            },
            success: function(response) {
                console.log('Ajax response:', response);
                if (response.success) {
                    alert('Messaggio inviato con successo.');
                    form[0].reset();
                } else {
                    alert('Errore nell\'invio del messaggio: ' + response.data.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Ajax error:', textStatus, errorThrown);
                console.log('Response:', jqXHR.responseText);
                alert('Si è verificato un errore durante l\'invio del messaggio.');
            }
        });
    });
});