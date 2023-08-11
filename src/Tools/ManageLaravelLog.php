<?php

namespace myodevops\ALTErnative\Tools;

use myodevops\ALTErnative\Models\LaravelLog;
use myodevops\ALTErnative\Models\Setting;

class ManageLaravelLog {
    private $storage_path;

    public function __construct() {
        $this->storage_path = env ('LARAVEL_LOG_PATH', storage_path('logs') . "\laravel.log");
    }

    /**
     * Process the laravel.log file if necessary
     *
     * @return bool TRUE if ok, else FALSE if error
     */
    public function processLog () {
        // Check if the datetime of the laravel.log file is changed from the last processing
        $LaravelLogLastTimeStamp = filemtime($this->storage_path);
        $LaravelLogLastDTText = date('Y-m-d H:i:s', $LaravelLogLastTimeStamp);
        $set = Setting::GetValue('LastLaravelLogProcessDateTime');
        if ($set === $LaravelLogLastDTText) {
            return TRUE;
        }

        // Process the laravel.log file an update the laravellogs table
        $handle = fopen($this->storage_path, "r");
        $records = [];
        $count = 0;
        $previousException = FALSE;
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // Find if it is a log line
                $logLine = $this->parseLogLine ($line);
                if ($logLine !== NULL) {
                    $count++;
                    $previousException = FALSE;
                    $logLine["id"] = $count;
                    array_push ($records, $logLine);
                } elseif (!$previousException) {
                    if (substr ($line, 0, 20) === "[previous exception]") {
                        $previousException = TRUE;
                    } else {
                        $stackLine = $this->parseStackLine ($line);
                        if ($stackLine !== NULL) {
                            array_push ($records[$count - 1]["stacktrace"], $stackLine); 
                        }
                    }
                }
            }
            fclose($handle);
        } else {
            $method = __METHOD__;
            $lastError = error_get_last();
            Log::Fatal ("Error opening the " . $this->storage_path . " file in $method.", $lastError['message']);
            return FALSE;
        }

        if ($this->saveInTable ($records) === FALSE) {
            return FALSE;
        }

        Setting::SetValue('LastLaravelLogProcessDateTime', $LaravelLogLastDTText);

        return TRUE;
    }

    /**
     * Check if the line of the log is a line with a message
     *
     * @param [type] $line
     * @return void
     */
    private function parseLogLine($line) {
        // Find the datetime
        if (strlen ($line) < 22) {
            return NULL;
        }
        if (($line[0] !== '[') && ($line[20] !== ']')) {
            return NULL;
        }

        try {
            $dateTime = new \DateTime(substr ($line, 1, 19));
            return [
                'datetime' => $dateTime,
                'message' => htmlspecialchars(substr ($line, 22)),
                'stacktrace' => []
            ];
        } catch (\Exception $e) {
            return NULL;
        }
    }

    /**
     * Check if the line of the log is a stacktrace line
     *
     * @param [type] $line
     * @return string The line of the trace
     */
    private function parseStackLine($line) {  
        if ($line[0] !== '#') {
            return NULL;
        }
        $firstspace = strpos ($line, ' ');
        if ($firstspace !== FALSE) {
            return htmlspecialchars($line);
        }
    
        return NULL;
    }

    private function saveInTable ($records) {
        $method = __METHOD__;

        LaravelLog::truncate ();
        foreach($records as $rec) {
            try {
                $laravellog = new LaravelLog();
                $laravellog->id = $rec["id"];
                $laravellog->datetime = $rec["datetime"];
                $laravellog->message = $rec["message"];
                $stacktext = "";
                foreach($rec["stacktrace"] as $stackline) {
                    if ($stacktext === "") {
                        $stacktext = $stackline;
                    } else {
                        $stacktext = $stacktext . $stackline;
                    }
                }
                $laravellog->stacktrace = $stacktext;
                $laravellog->save ();
            } catch (\Exception $e) {
                Log::Fatal ("Error saving the Laravel log records in $method.", $e->getMessage());
                return NULL;
            }
        }
    }
}