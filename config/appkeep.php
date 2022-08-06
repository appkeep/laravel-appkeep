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

    /**
     * To enable backups, run:
     * php artisan appkeep:backups
     */
    'backups' => [

        /**
         * Turn on/off backups.
         */
        'enabled' => env('APPKEEP_BACKUPS_ENABLED', false),

        /**
         * What time you want the backups to run?
         */
        'run_at' => env('APPKEEP_BACKUPS_RUN_AT', '02:00'),

        /**
         * The disk names on which the backups will be stored.
         * You can store your backups both on-site and off-site.
         *
         * To view and configure your disks, see:
         * config/filesystems.php
         */
        'destination' => [
            'local',
            // 's3',
        ],

        'files' => [
            /**
             * Should we include files in the backup?
             * You can set this to false to only backup your database.
             */
            'enabled' => true,

            /**
             * What directories would you like to exclude?
             * Directories used by the backup process will automatically be excluded.
             */
            'exclude' => [
                base_path('.git'),
                base_path('vendor'),
                base_path('node_modules'),
            ],
        ],

        'database' => [
            /**
             * Should we include database tables in the backup?
             */
            'enabled' => true,

            /**
             * Which connection should we use?
             */
            'connection' => env('DB_CONNECTION', 'mysql'),

            /**
             * Exclude tables from the backup
             */
            'exclude' => [
                // 'password_resets',
            ],
        ],

        /**
         * How much diskspace should be used for backups in total (in megabytes)?
         * If backups end up using more space than this, older ones will be deleted.
         */
        'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
    ],
];
