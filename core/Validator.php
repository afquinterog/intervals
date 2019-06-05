<?php

namespace App\Core;

class Validator
{
    /**
     * Validate a date
     *
     * @param  string $date
     * @return boolean
     */
    public function date($value)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate a numeric value
     *
     * @param  string $value
     * @return boolean
     */
    public function number($value)
    {
        if (is_numeric($value)) {
            return true;
        } else {
            return false;
        }
    }
}
