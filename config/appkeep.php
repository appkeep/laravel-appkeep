<?php


return [

    /**
     * Don't change this URL, unless you have a good reason.
     */
    'endpoint' => 'https://appkeep.dev/api/v1/health',

    /**
     * Make sure to set this in your .env file.
     */
    'key' => env('APPKEEP_KEY'),

    /**
     * Name of your server.
     * Useful if you have multiple servers or running multiple apps on the same server.
     */
    'server' => env('APPKEEP_SERVER_NAME', 'default'),
];
