<?php


return [

    /**
     * Appkeep url.
     */
    'url' => env('APPKEEP_URL', 'https://appkeep.dev'),

    /**
     * Make sure to set this in your .env file.
     */
    'secret' => env('APPKEEP_SECRET'),

    /**
     * Name of your server.
     * Useful if you have multiple servers or running multiple apps on the same server.
     */
    'server' => env('APPKEEP_SERVER', 'default'),
];
