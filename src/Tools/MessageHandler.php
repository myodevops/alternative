<?php 

namespace myodevops\ALTErnative\Tools;

class JsonMessageType {
    const OPERATIVE_SUCCESS = '_operativeSuccess';
    const OPERATIVE_INFO = '_operativeInfo';
    const OPERATIVE_WARNING = '_operativeWarning';
    const OPERATIVE_ERROR = '_operativeError';
    const RUNTIME_ERROR = '_runtimeError';
}

class MessageHandler {
    static function jsonMessage ($type, $message, $infoArray = []) {
        if (!(is_array($infoArray) || is_object($infoArray))) {
            $type = JsonMessageType::RUNTIME_ERROR;
            $message = "Parameter infoArray in MessageHandler is wrong (" . gettype($infoArray) . ")";
            $infoArray = [];            
        }

        switch ($type) {
            case JsonMessageType::OPERATIVE_INFO:
                $keyType = JsonMessageType::OPERATIVE_INFO;
                break;
            case JsonMessageType::OPERATIVE_SUCCESS:
                $keyType = JsonMessageType::OPERATIVE_SUCCESS;
                break;
            case JsonMessageType::OPERATIVE_WARNING:
                $keyType = JsonMessageType::OPERATIVE_WARNING;
                break;
            case JsonMessageType::OPERATIVE_ERROR:
                $keyType = JsonMessageType::OPERATIVE_ERROR;
                break;
            case JsonMessageType::RUNTIME_ERROR:
                $keyType = JsonMessageType::RUNTIME_ERROR;
                break;
        }

        $objMessage = [ $keyType => []];

        $objMessage[$keyType]["message"] = $message;

        if (!empty ($infoArray)) {
            foreach ($infoArray as $key => $value) {
                $objMessage[$keyType][$key] = $value;
            }
        }

        return $objMessage;
    }
}