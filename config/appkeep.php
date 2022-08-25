<?php

return [
    /**
     * Don't change this URL, unless you have a good reason.
     */
    'endpoint' => 'https://appkeep.co/api/v1/health',

    /**
     * Make sure to set this in your .env file.
     */
    'key' => env('APPKEEP_KEY'),

    /**
     * This is handled by the package out of the box.
     *
     * But if the default method doesn't work for you,
     * you can provide your own a UUIDv4 value string.
     * @see vendor/appkeep/laravel-appkeep/src/Diagnostics/Server.php
     */
    'server' => env('APPKEEP_SERVER_UID'),
];
