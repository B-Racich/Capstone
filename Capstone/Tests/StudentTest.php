<?php

use PHPUnit\Framework\TestCase;
include_once __DIR__.'/../public_html/model/Student.php';
include_once __DIR__.'/../lib/Database.class.php';

class StudentTest extends TestCase {

    private $Student;

    public function setup() {
        $this->Student = new Student();
        $this->Student->loadStudent('PHP_UNIT_TEST');
    }

    public function testCreateStudent() {
        $this->assertTrue($this->Student->createStudent('PHP_UNIT_TEST', 'test', 'John', 'Doe', 'valid@email.com', 0000000, 0));
    }

    public function testLoginStudent() {
        $this->assertEquals('Login succeeded', $this->Student->loginStudent('PHP_UNIT_TEST', 'test'));
    }

    public function testLoadStudent() {
        $this->assertTrue($this->Student->loadStudent('PHP_UNIT_TEST'));
    }

    public function testIsStudent() {
        $this->assertTrue($this->Student->isStudent());
    }


    //  Not passing in travis
//    public function testGetSID() {
//        $this->assertStringMatchesFormat('%d', $this->Student->getSID());
//    }

    public function testPrintInfo() {
        $expected = "PHP_UNIT_TEST,".$this->Student->getUID().",John,Doe,valid@email.com,0,0";
        $this->assertEquals($expected, $this->Student->printInfo());
    }

    public function testDeleteStudent() {
        $this->assertTrue($this->Student->deleteStudent('PHP_UNIT_TEST'));
    }

//    public function testCanTA() {
//        $S = new Student();
//        $S->loadStudent('remotetest');
//        $S->loadApplications();
//        $app = $S->getApplication(2018, 'W');
//        $c = new Course(226, array('year' => 2018, 'scode' => 'W', 'term' => 1));
//        $this->assertTrue($S->canTA($c, $app) == 0);
//        print_r($S->canTA($c, $app));
//    }

}