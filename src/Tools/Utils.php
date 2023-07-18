<?php 

namespace myodevops\ALTErnative\Tools;

class Utils {
    /**
     * This function check if exists values or if the values are not empty of the array $array
     * but only for the values that are the key present in the $keyToCheck array
     *
     * @param Array $array Array to check
     * @param Array $keysToCheck
     * @return String The key that haven't a value or if its value is empty
     */
    static function checkEmptyValues($array, $keysToCheck) {
        foreach ($keysToCheck as $key) {
            if (!isset($array[$key]) || empty($array[$key])) {
                return $key;
            }
        }
    
        return null;
    }
}