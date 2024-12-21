<?php

namespace Squartup\RistoPos\Install;

class Installer
{
    public function __construct(Type $var = null) {
        // Esegui questa funzione all'attivazione del plugin
        // register_activation_hook(__FILE__, 'ristopos_add_custom_roles_and_capabilities');

        // // Esegui questa funzione anche quando il plugin viene aggiornato
        // add_action('plugins_loaded', 'ristopos_add_custom_roles_and_capabilities');
    }
}