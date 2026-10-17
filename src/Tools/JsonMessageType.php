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
