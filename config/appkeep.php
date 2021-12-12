<?php


return [
    /**
     * Make sure to set this in your .env file.
     */
    'secret' => env('APPKEEP_SECRET'),

    /**
     * Name of your server.
     * Useful if you have multiple servers or running multiple apps on the same server.
     */
    'server' => env('APPKEEP_SERVER_NAME', 'my-server'),
];
