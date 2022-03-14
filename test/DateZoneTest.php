<?php
use PHPUnit\Framework\TestCase;
require 'class/DateZoneConverter.php';

class DateZoneTest extends TestCase{

    public function testReturnDateBerlin(){
        $dz = new DateZoneConverter('1.0.15+42',  "2019-09-04 08:00:00");
        $this->assertEquals($dz->get_formatted_date(),'2019-09-04 08:00:00');
    }

    public function testReturnDateUTC(){
        $dz = new DateZoneConverter('1.0.20',  "2019-09-04 08:00:00");
        $this->assertNotEquals($dz->get_formatted_date(),'2019-09-04 08:00:00');
    }
}
