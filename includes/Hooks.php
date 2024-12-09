<?php

namespace Squartup\RistoPos;

class Hooks
{
    /**
     * Constructor.
     *
     * @since 0.1.0
     */
    public function __construct()
    {
        add_action('init', [$this, 'setDefaultTimeZone']);
    }

    public function setDefaultTimeZone()
    {
        date_default_timezone_set('Europe/Rome');
    }
}