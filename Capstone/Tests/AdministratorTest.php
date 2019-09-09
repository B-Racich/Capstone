<?php

use PHPUnit\Framework\TestCase;
include_once __DIR__.'/../public_html/model/Administrator.php';
include_once __DIR__.'/../lib/Database.class.php';

class AdministratorTest extends TestCase {

    private $Administrator;

    public function setup() {
        $this->Administrator = new Administrator();
        $this->Administrator->loadAdministrator('PHP_UNIT_TEST');
    }

    public function testCreateAdministrator() {
        $this->assertTrue($this->Administrator->createAdministrator('PHP_UNIT_TEST', 'test', 'John', 'Doe', 'valid@email.com'));
    }

    public function testLoginAdministrator() {
        $this->assertTrue($this->Administrator->isAdministrator());
    }

    public function testGetAccountType() {
        $this->assertEquals($this->Administrator->getAccountType(), 'administrator');
    }

    public function testLoadAdministrator() {
        $this->assertTrue($this->Administrator->loadAdministrator('PHP_UNIT_TEST'));
    }

    public function testIsAdministrator() {
        $this->assertTrue($this->Administrator->isAdministrator());
    }

    public function testPrintInfo() {
        $expected = "PHP_UNIT_TEST,".$this->Administrator->getUID().",John,Doe,valid@email.com,".$this->Administrator->GetAID();
        $this->assertEquals($expected, $this->Administrator->printInfo());
    }

    public function testDeleteAdministrator() {
        $this->assertTrue($this->Administrator->deleteAdministrator());
    }



}