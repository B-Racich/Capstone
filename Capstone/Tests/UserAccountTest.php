<?php

    use PHPUnit\Framework\TestCase;
    include_once __DIR__.'/../public_html/model/UserAccount.php';
    include_once __DIR__.'/../public_html/model/Student.php';
    include_once __DIR__.'/../public_html/model/Administrator.php';
    include_once __DIR__.'/../lib/Database.class.php';

    class UserAccountTest extends TestCase {

        private $UserAccount;
        private $Student;
        private $Administrator;

        public function setup() {
                $this->UserAccount = new UserAccount();
        }

        public function teardown() {

        }

        public function testCreateUserAccount() {
            $this->assertTrue($this->UserAccount->createUserAccount('PHP_UNIT_TEST', 'test', 'John', 'Doe', 'valid@email.com'));
        }

        public function testLoadUserAccount() {
            $this->assertTrue($this->UserAccount->loadUserAccount('PHP_UNIT_TEST'));
        }

        public function testPrintInfo() {
            $this->UserAccount->loadUserAccount('PHP_UNIT_TEST');
            $expected = "PHP_UNIT_TEST,".$this->UserAccount->getUID().",John,Doe,valid@email.com";
            $this->assertEquals($expected, $this->UserAccount->printInfo());
        }

        public function testChangeFirstName() {
            $this->UserAccount->loadUserAccount('PHP_UNIT_TEST');
            $expected = "Jack";
            $this->UserAccount->changeFirstName($expected);
            $results = explode(',', $this->UserAccount->printInfo());
            $this->assertEquals($expected, $results[2]);
        }

        public function testChangeLastName() {
            $this->UserAccount->loadUserAccount('PHP_UNIT_TEST');
            $this->assertTrue($this->UserAccount->changeLastName('Joe'));
        }

        public function testChangeEmail() {
            $this->UserAccount->loadUserAccount('PHP_UNIT_TEST');
            $this->assertTrue($this->UserAccount->changeEmail('test@test.com'));
        }

        public function testChangePassword() {
            $this->UserAccount->loadUserAccount('PHP_UNIT_TEST');
            $this->assertTrue($this->UserAccount->changePassword('test', 'testHash'));
        }

        //  Not passing in travis
//        public function testGetUID() {
//            $this->assertStringMatchesFormat('%d', $this->UserAccount->getUID());
//        }

        public function testDeleteUser() {
            $this->assertTrue($this->UserAccount->deleteUserAccount('PHP_UNIT_TEST'));
        }

    public function testGetAccountTypeAdministrator() {
        $this->Administrator = new Administrator();
        $this->Administrator->createAdministrator('PHP_UNIT_TEST', 'test', 'John', 'Doe', 'valid@email.com');

        $expected = "administrator";
        $this->assertEquals($expected, $this->UserAccount->lookUpUser('PHP_UNIT_TEST'));
        $this->Administrator->deleteAdministrator();
    }

    public function testGetAccountTypeStudent() {
        $this->Student = new Student();
        $this->Student->createStudent('PHP_UNIT_TEST', 'test', 'John', 'Doe', 'valid@email.com', 0, 0);

        $expected = "student";
        $this->assertEquals($expected, $this->UserAccount->lookUpUser('PHP_UNIT_TEST'));
        $this->Student->deleteStudent();

    }

    }