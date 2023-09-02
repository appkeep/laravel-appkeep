<?php

namespace Appkeep\Laravel\Listeners;

use Exception;

class SlowQueryHandler
{
    static $fileName = "tmp_slow_query.log";
    public static function handle($filename,  $event, $context)
    {
        $fp = fopen($filename, "a+");

        $slowQueryEvent = [
            'event' => $event,
            'context' => $context,
        ];

        $eventInfo = json_encode(array_filter($slowQueryEvent, function ($key) {
            return $key != 'connection';
        }, ARRAY_FILTER_USE_KEY));

        if (flock($fp, LOCK_EX)) {
            // Truncate last bracket of json
            $stat = fstat($fp);
            if ($stat['size'] == 0) {
                fwrite($fp, '[');
                $toAppend =  $eventInfo . ']';
            } else {
                $toAppend = ', ' . $eventInfo . ']';
                ftruncate($fp, $stat['size'] - 1);
            }
            // Append the next object
            fwrite($fp, $toAppend);
            fflush($fp);
            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }

    public static function collectSlowQueriesFromFile(string $filename, bool $clear)
    {
        if (file_exists($filename)) {

            $data = [];
            $fp = fopen($filename, "r+");

            if (flock($fp, LOCK_EX)) {
                $stat = fstat($fp);
                if (!$stat['size'] == 0) {
                    $contents = fread($fp, $stat['size']);
                    $data = json_decode($contents, true);
                    if ($clear) {
                        ftruncate($fp, 0);
                    }
                    fflush($fp);
                }
                flock($fp, LOCK_UN);
            }

            fclose($fp);
            return $data;
        } else {
            throw new Exception('File does not exist');
        }
    }
}
