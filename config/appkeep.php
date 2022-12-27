<?php

return [
    /**
     * Don't change this URL, unless you have a good reason.
     */
    'endpoint' => 'https://appkeep.co/api/v1/events',

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

    /**
     * Used to verify requests coming to explore endpoint are actually coming from Appkeep.
     * Please DO NOT MODIFY this array unless you know what you're doing.
     */
    'our_servers' => [
        '12ca17b49af2289436f303e0166030a21e525d266e209267433801a8fd4071a0',
        'b044c7fbeef04423ab68e12b2335794f9bcdb92eaac1dcbbbdae39dc5e5e314c',
    ],
];
