<?php 

namespace myodevops\ALTErnative\Tools;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

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

    static function Debug ($function, $message)  {
        self::writeRecord ($function, $message, 'debug');
    }

    private static function writeRecord ($function, $message, $type) {
        $info = '<' . filter_input(INPUT_SERVER, 'REQUEST_URI') . '> <' . basename(__FILE__) . '> <' . filter_input(INPUT_SERVER, 'REMOTE_ADDR') . '>';
        $query = "INSERT INTO errorlogs
                  (message, userid, type, info, datetime)
                  VALUES (:message, :userid, :type, :info, :datetime)";
        $userid = Auth::id();
        if (isNull($userid)) {
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