<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Contracts\Support\Arrayable;

class ServerContext implements Arrayable
{
    public static function isUbuntu()
    {
        return strpos(strtolower(php_uname()), 'ubuntu') !== false;
    }

    public function toArray()
    {
        return [
            'name' => gethostname(),
            'uid' => self::uniqueIdentifier(),
        ];
    }

    public static function uniqueIdentifier()
    {
        if ($uuid = config('appkeep.server')) {
            // validate that it's uuid4
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid)) {
                throw new \Exception(
                    join("\n", [
                        'APPKEEP_SERVER_UID must be in UUID v4 format.',
                        'You can generate one here: https://www.uuidgenerator.net/',
                        'Leave it blank to let Appkeep create a unique id.',
                    ])
                );
            }
        }

        $identifiers = [];

        // Fully qualified domain name
        $fqdn = self::fqdn();

        if ($fqdn !== 'localhost') {
            $identifiers[] = $fqdn;
        }

        // If machine-id is readable, add that to the mix.
        if (is_readable('/etc/machine-id')) {
            $identifiers[] = str_replace("\n", "", file_get_contents('/etc/machine-id'));
        }

        if (empty($identifiers)) {
            throw new \Exception(
                join("\n", [
                    'We couldn\'t generate a unique identifier for your server.',
                    'Please set a uuidv4 value yourself for APPKEEP_SERVER_UID in your .env file.',
                    'You can generate one here: https://www.uuidgenerator.net/',
                ])
            );
        }

        return hash('sha256', join(".", $identifiers));
    }

    private static function fqdn()
    {
        $fqdn = gethostbyaddr('127.0.0.1');

        return ($fqdn !== 'localhost') ? $fqdn : gethostname();
    }
}
