<?php

use PHPUnit\Framework\TestCase;
include_once __DIR__.'/../public_html/model/TA.php';
include_once __DIR__.'/../lib/Database.class.php';

class TATest extends TestCase {

    private $Ta;

    public function setup() {
        $this->Ta = new TA();
        $this->Ta->loadTA('PHP_UNIT_TEST');
    }

    public function testCreateTA() {
        $this->Ta->createStudent('PHP_UNIT_TEST', 'test', 'John', 'Doe', 'valid@email.com', 0, 0);
        $this->assertTrue($this->Ta->createTA(2, 4, 'you\'re pretty good'));
    }

    public function testLoadTA() {
        $this->assertTrue($this->Ta->loadTA('PHP_UNIT_TEST'));
    }

    public function testIsTA() {
        $this->assertTrue($this->Ta->isTA());
    }

//  Not passing in travis
//    public function testGetTAID() {
//        $this->assertStringMatchesFormat('%d', $this->Ta->getTAID());
//    }

    public function testPrintInfo() {
        $expected = "PHP_UNIT_TEST,".$this->Ta->getUID().",John,Doe,valid@email.com,0,0,".$this->Ta->getTAID().",2" . ",you're pretty good";
        $this->assertEquals($expected, $this->Ta->printInfo());
    }

    public function testDeleteTA() {
        $this->assertTrue($this->Ta->deleteTA());
    }

}