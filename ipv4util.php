<?php
/**
 * IPv4 utility class.
 *
 * @package ipv4util
 * @author John Peloquin
 * @copyright Copyright (c) 2013 John Peloquin. All rights reserved.
 */

/**
 * IPv4 utility class.
 *
 * @package ipv4util
 */
class IPv4Util {
    const IP_REGEX = '/^(?:\d{1,3}\.){3}\d{1,3}$/';
    const CIDR_REGEX = '/^((?:\d{1,3}\.){3}\d{1,3})\/(\d{1,2})$/';
    
    const INVALID_ARGUMENT = 'Invalid argument';
    
    /**
     * Logical right shift.
     *
     * @param int $n number
     * @param int $b number of bits to shift
     * @return int shifted number
     */
    public static function lrs($n, $b) {
        $s = 8 * PHP_INT_SIZE;
        
        if($b < 0 || $b > $s) {
            throw new InvalidArgumentException(self::INVALID_ARGUMENT);
        }
        
        return ($b === 0) ? $n : (($n >> $b) & (PHP_INT_MAX >> ($b - 1)));
    }
    
    /**
     * Matches IP address against CIDR range.
     *
     * @param string IP address string (e.g. '192.168.1.1')
     * @param string CIDR range string (e.g. '192.168.1.0/24')
     * @return bool true if match, false otherwise
     */
    public static function cidr_match($ip, $cidr) {
        if(!preg_match(self::IP_REGEX, $ip) || !preg_match(self::CIDR_REGEX, $cidr, $parts)) {
            throw new InvalidArgumentException(self::INVALID_ARGUMENT);
        }
        
        $ip = ip2long($ip);
        $n = ip2long($parts[1]);
        $b = 32 - intval($parts[2]);
        
        if($ip === false || $n === false || $b < 0 || $b > 32) {
            throw new InvalidArgumentException(self::INVALID_ARGUMENT);
        }
        
        return ((self::lrs($ip, $b) << $b) === (self::lrs($n, $b) << $b));
    }
}
