<?php

namespace myodevops\ALTErnative\Tools;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Log {
    static function Warning ($function, $message)  {
        self::writeRecord ($function, $message, 'warning');
    }

    static function Error ($function, $message)  {
        self::writeRecord ($function, $message, 'error');
    }

    static function Fatal ($function, $message)  {
        self::writeRecord ($function, $message, 'fatal');
    }

    static function Notify ($function, $message)  {
        self::writeRecord ($function, $message, 'notify');
    }

    static function Debug ($function, $message)  {
        self::writeRecord ($function, $message, 'debug');
    }

    private static function writeRecord ($function, $message, $type) {
        // Find the file and the line of the application code that called the log:
        // [0] is the call to writeRecord from Warning/Error/..., [1] is the call from the application
        $trace = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset ($trace[1]['file']) ? basename ($trace[1]['file']) . ':' . $trace[1]['line'] : '';

        $info = '<' . request()->getRequestUri() . '> <' . $caller . '> <' . request()->ip() . '>';
        $query = "INSERT INTO errorlogs
                  (message, userid, type, info, datetime)
                  VALUES (:message, :userid, :type, :info, :datetime)";
        $userid = Auth::id();
        if (is_null($userid)) {
            $userid = 0;
        }
        DB::connection('altesqlite')->statement ($query, [
            'message' => $function . ": " . $message,
            'userid' => $userid,
            'type' => $type,
            'info' => $info,
            'datetime' => time(),
        ]);
    }
}
