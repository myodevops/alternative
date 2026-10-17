<?php 

namespace myodevops\ALTErnative\Tools;

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
            default:
                // Unknown type: show a runtime error, keeping the original message
                $keyType = JsonMessageType::RUNTIME_ERROR;
                $message = "Wrong message type (" . (is_scalar($type) ? $type : gettype($type)) . ") in MessageHandler: " . $message;
                $infoArray = [];
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