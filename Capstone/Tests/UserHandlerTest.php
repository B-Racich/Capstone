<?php
    use PHPUnit\Framework\TestCase;
    include_once __DIR__.'/../public_html/model/UserHandler.php';
    include_once __DIR__.'/../public_html/model/UserAccount.php';
    include_once __DIR__.'/../public_html/model/Student.php';
    include_once __DIR__.'/../public_html/model/Administrator.php';
    include_once __DIR__.'/../lib/Database.class.php';

    class UserHandlerTest extends TestCase {

        private $handlerObject;
        private $expected;

        public function setup() {
            $_POST['userName'] = 'PHP_UNIT_TEST';
            $_POST['password'] = 'test';
            $_POST['firstName'] = 'John';
            $_POST['lastName'] = 'Doe';
            $_POST['email'] = 'valid@email.com';
            $_POST['sID'] =  0;
            $_POST['graduate'] = 0;

            $_POST['string'] = 'Handler Test';
            $_POST['newPassword'] = 'testHash';
        }

        public function testCallCreateStudent() {
            $this->expected = 'createStudent';
            $this->handlerObject = new UserHandler($this->expected);
            $this->handlerObject->callFunction();
            $this->assertEquals($this->handlerObject->testExecuted(), true);
        }

        public function testCallLoginAccountStudent() {
            $this->expected = 'loginAccount';
            $this->handlerObject = new UserHandler($this->expected);
            $this->handlerObject->callFunction();
            $this->assertEquals('Login succeeded', $this->handlerObject->testExecuted());
        }

        public function testPrintInfo() {
            $_SESSION = array();
            $student = new Student();
            $student->loadStudent('PHP_UNIT_TEST');
            $_SESSION['User'] = serialize($student);

            $this->expected = 'printInfo';
            $this->handlerObject = new UserHandler($this->expected);
            $this->handlerObject->callFunction();
            $this->assertTrue(is_string($this->handlerObject->testExecuted()), "Got a " . gettype($this->handlerObject->testExecuted()) . " instead of a string");
        }

        public function testCallDeleteStudent() {
            $_SESSION = array();
            $student = new Student();
            $student->loadStudent('PHP_UNIT_TEST');
            $_SESSION['User'] = $student;

            $this->expected = 'deleteStudent';
            $this->handlerObject = new UserHandler($this->expected);
            $this->handlerObject->callFunction();
            $this->assertEquals($this->handlerObject->testExecuted(), true);
        }

        public function testCallCreateAdministrator() {
            $this->expected = 'createAdministrator';
            $this->handlerObject = new UserHandler($this->expected);
            $this->handlerObject->callFunction();
            $this->assertEquals($this->handlerObject->testExecuted(), true);
        }

        public function testCallLoginAccountAdministrator() {
            $this->expected = 'loginAccount';
            $this->handlerObject = new UserHandler($this->expected);
            $this->handlerObject->callFunction();
            $this->assertEquals('Login succeeded', $this->handlerObject->testExecuted());
        }

        public function testCallDeleteAdministrator() {
            $_SESSION = array();
            $_SESSION['User'] = new Administrator();
            $_SESSION['User']->loadAdministrator('PHP_UNIT_TEST');

            $this->expected = 'deleteAdministrator';
            $this->handlerObject = new UserHandler($this->expected);
            $this->handlerObject->callFunction();
            $this->assertEquals($this->handlerObject->testExecuted(), true);
        }

    }