<?php
/**
 * IPv4 utility class tests.
 *
 * @package ipv4util
 * @author John Peloquin
 * @copyright Copyright (c) 2013 John Peloquin. All rights reserved.
 */

/**
 * IPv4 utility class.
 */
require_once('../ipv4util.php');

/**
 * IPv4 utility tests.
 */
class IPv4UtilTest extends PHPUnit_Framework_TestCase {
    public function get_data_for_lrs() {
        return array(
            // 32-bits
            array(0x00000000, 0, 0x00000000),
            array(0x00000001, 0, 0x00000001),
            array(0x80000000, 0, 0x80000000),
            array(0x0F0F0F0F, 0, 0x0F0F0F0F),
            
            array(0xFFFFFFFF, 0, 0xFFFFFFFF),
            array(0xFFFFFFFE, 0, 0xFFFFFFFE),
            array(0x7FFFFFFF, 0, 0x7FFFFFFF),
            array(0xF0F0F0F0, 0, 0xF0F0F0F0),
            
            array(0x00000000, 1, 0x00000000),
            array(0x00000001, 1, 0x00000000),
            array(0x80000000, 1, 0x40000000),
            array(0x0F0F0F0F, 1, 0x07878787),
            
            array(0xFFFFFFFF, 1, 0x7FFFFFFF),
            array(0xFFFFFFFE, 1, 0x7FFFFFFF),
            array(0x7FFFFFFF, 1, 0x3FFFFFFF),
            array(0xF0F0F0F0, 1, 0x78787878),
            
            array(0x00000000, 4, 0x00000000),
            array(0x00000001, 4, 0x00000000),
            array(0x80000000, 4, 0x08000000),
            array(0x0F0F0F0F, 4, 0x00F0F0F0),
            
            array(0xFFFFFFFF, 4, 0x0FFFFFFF),
            array(0xFFFFFFFE, 4, 0x0FFFFFFF),
            array(0x7FFFFFFF, 4, 0x07FFFFFF),
            array(0xF0F0F0F0, 4, 0x0F0F0F0F),
            
            array(0x00000000, 32, 0x00000000),
            array(0x00000001, 32, 0x00000000),
            array(0x80000000, 32, 0x00000000),
            array(0x0F0F0F0F, 32, 0x00000000),
            
            array(0xFFFFFFFF, 32, 0x00000000),
            array(0xFFFFFFFE, 32, 0x00000000),
            array(0x7FFFFFFF, 32, 0x00000000),
            array(0xF0F0F0F0, 32, 0x00000000)
        );
    }
    
    public function get_data_for_cidr_match() {
        return array(
            // yes
            array('1.2.3.4', '0.0.0.0/0', true),
            array('1.2.3.4', '9.9.9.9/0', true),
            array('1.2.3.4', '1.0.0.0/8', true),
            array('1.2.3.4', '1.9.9.9/8', true),
            array('1.2.3.4', '1.2.0.0/16', true),
            array('1.2.3.4', '1.2.9.9/16', true),
            array('1.2.3.4', '1.2.3.0/24', true),
            array('1.2.3.4', '1.2.3.9/24', true),
            array('1.2.3.4', '1.2.3.4/32', true),
            // no
            array('1.2.3.4', '0.0.0.0/8', false),
            array('1.2.3.4', '1.0.0.0/16', false),
            array('1.2.3.4', '1.2.0.0/24', false),
            array('1.2.3.4', '1.2.3.0/32', false)
        );
    }
    
    public function get_data_for_cidr_match_invalid() {
        return array(
            // invalid ip
            array('', '0.0.0.0/0'),
            array(' ', '0.0.0.0/0'),
            array('1', '0.0.0.0/0'),
            array('1.2', '0.0.0.0/0'),
            array('1.2.3.256', '0.0.0.0/0'),
            array('A.B.C.D', '0.0.0.0/0'),
            
            // invalid cidr
            array('1.2.3.4', ''),
            array('1.2.3.4', ' '),
            array('1.2.3.4', '/'),
            array('1.2.3.4', '//'),
            
            array('1.2.3.4', '/0'),
            array('1.2.3.4', ' /0'),
            array('1.2.3.4', '0/0'),
            array('1.2.3.4', '0.0/0'),
            array('1.2.3.4', '0.0.0.256/0'),
            array('1.2.3.4', 'A.B.C.D/0'),
            
            array('1.2.3.4', '0.0.0.0/'),
            array('1.2.3.4', '0.0.0.0/ '),
            array('1.2.3.4', '0.0.0.0/0.0'),
            array('1.2.3.4', '0.0.0.0/33'),
            array('1.2.3.4', '0.0.0.0/A')
        );
    }
    
    /**
     * @dataProvider get_data_for_lrs
     */
    public function test_lrs($n, $b, $expected) {
        $this->assertEquals($expected, IPv4Util::lrs($n, $b));
    }
    
    /**
     * @dataProvider get_data_for_cidr_match
     */
    public function test_cidr_match($ip, $cidr, $expected) {
        $this->assertEquals($expected, IPv4Util::cidr_match($ip, $cidr));
    }
    
    /**
     * @dataProvider get_data_for_cidr_match_invalid
     * @expectedException InvalidArgumentException
     */
    public function test_cidr_match_invalid($ip, $cidr) {
        IPv4Util::cidr_match($ip, $cidr);
    }
}
