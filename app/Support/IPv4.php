<?php

namespace App\Support;

class IPv4
{
    public static function isValidIPv4Net($ipv4Network)
    {
        return preg_match('#^(?:((?:0)|(?:2(?:(?:[0-4][0-9])|(?:5[0-5])))|(?:1?[0-9]{1,2}))\.((?:0)|(?:2(?:(?:[0-4][0-9])|(?:5[0-5])))|(?:1?[0-9]{1,2}))\.((?:0)|(?:2(?:(?:[0-4][0-9])|(?:5[0-5])))|(?:1?[0-9]{1,2}))\.((?:0)|(?:2(?:(?:[0-4][0-9])|(?:5[0-5])))|(?:1?[0-9]{1,2}))(?:/((?:(?:0)|(?:3[0-2])|(?:[1-2]?[0-9]))))?)$#', $ipv4Network);
    }
}