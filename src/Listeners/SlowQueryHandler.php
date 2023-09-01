<?php

namespace Appkeep\Laravel\Listeners;

class SlowQueryHandler
{
    static $fileName = "tmp_slow_query.log";
    public static function handle($filename,  $event)
    {
        $fp = fopen($filename, "a+");

        $eventInfo = json_encode(array_filter((array)$event, function ($key) {
            return $key != 'connection';
        }, ARRAY_FILTER_USE_KEY));

        // dd($toAppend);
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
}
