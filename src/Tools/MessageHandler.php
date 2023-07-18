<?php 

namespace myodevops\ALTErnative\Tools;

/**
 * Define the various type of message that can be displayed by the MessageHandler class
 * The available values are:
 *   JsonMessageType::OPERATIVE_SUCCESS A success message is displayed
 *   JsonMessageType::OPERATIVE_INFO    A info message is displayed
 *   JsonMessageType::OPERATIVE_WARNING A warning message is displayed
 *   JsonMessageType::OPERATIVE_ERROR   An operative error message is displayed
 *   JsonMessageType::RUNTIME_ERROR     A runtime error message is displayed
 */
class JsonMessageType {
    const OPERATIVE_SUCCESS = '_operativeSuccess';
    const OPERATIVE_INFO = '_operativeInfo';
    const OPERATIVE_WARNING = '_operativeWarning';
    const OPERATIVE_ERROR = '_operativeError';
    const RUNTIME_ERROR = '_runtimeError';
}

class MessageHandler {
    /**
     * Generate a json message that is compatible for viewing in the ALTernative WS management
     * The format is specified for viewing messages and errors in the ALTernative UI
     *
     * @param JsonMessageType $type Type of message. The values can be a constant property of JsonMessageType class
     * @param String $message The message to be display
     * @param array $infoArray A set of additional info
     * @return Json A json object for displaying the message compatible with the ALTernative UI
     */
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