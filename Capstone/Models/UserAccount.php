<?php

include_once __DIR__.'/../../lib/Database.class.php';

    class UserAccount {

        protected $userName;
        protected $uID;
        protected $firstName;
        protected $lastName;
        protected $email;

        protected $type =  'user';

        protected $initialized = false;
        protected $built = false;

        protected $conn;

        /**
         * UserAccount constructor.
         */
        public function __construct(){
            $this->initialized = true;
            return $this;
        }

        /**
         * Database connections for SQL queries
         */
        protected function openConn() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }

        protected function closeConn() {
            $this->conn = null;
            $database = null;
        }

        /** Function to create a new UserAccount in the DB, sets the UserAccount object variables
         * @param $userName
         * @param $password
         * @param $firstName
         * @param $lastName
         * @param $email
         * @return bool Success
         */
        public function createUserAccount($userName, $password, $firstName, $lastName, $email) {
            /*
             * Perform SQL query to save new user account
             */
            $this->openConn();
            $sql = "INSERT INTO UserAccount (userName, passHash, firstName, lastName, email) VALUES(:userName, :passHash, :firstName, :lastName, :email)";
            $stmt = $this->conn->prepare($sql);
            //This function hashes the password with bcrypt default, storing salt within the hash
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute(array(':userName' => $userName, ':passHash' => $passHash, ':firstName' => $firstName, ':lastName' => $lastName, ':email' => $email));

            if ($stmt->rowCount() > 0) {
                //SQL to get uID
                $sql = "SELECT uID FROM UserAccount WHERE userName = :userName";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':userName' => $userName));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                //Set Variables
                $this->userName = $userName;
                $this->uID = $row['uID'];
                $this->firstName = $firstName;
                $this->lastName = $lastName;
                $this->email = $email;
                $results = true;
                $this->built = true;
            } else {
                $results = false;
            }

            $this->closeConn();
            return $results;
        }


        public function getAccountType() {
            return $this->type;
        }

        public function lookUpUser($userName) {
            $this->openConn();
            $sql = "SELECT * FROM UserAccount, Administrator WHERE userName = :userName AND UserAccount.uID = Administrator.uID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':userName' => $userName));

            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->closeConn();
                return 'administrator';
            }
            else {
                $sql = "SELECT * FROM UserAccount, Student WHERE userName = :userName AND UserAccount.uID = Student.uID";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':userName' => $userName));
                if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->closeConn();
                    return 'student';
                }
                else {
                    $this->closeConn();
                    return 'user';
                }
            }
        }

        public function loadUserAccount($userName) {
            $this->openConn();
            $sql = "SELECT * FROM UserAccount WHERE userName = :userName";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':userName' => $userName));

            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->userName = $row['userName'];
                $this->uID = $row['uID'];
                $this->firstName = $row['firstName'];
                $this->lastName = $row['lastName'];
                $this->email = $row['email'];
                $results = true;
                $this->built = true;
            }
            else {
                $results = false;
            }
            $this->closeConn();
            return $results;
        }

        /** Function to change UserAccount firstName
         * @param $string   String to change firstName to
         * @return bool     Success
         */
        public function changeFirstName($string) {
            /*
             * Perform SQL query and return boolean if success.
             */
            $this->openConn();
            $sql = "UPDATE UserAccount SET firstName = :firstName WHERE uID = :uID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':firstName' => $string, ':uID' => $this->uID));

            if($stmt->rowCount() > 0) {
                $this->firstName = $string;
                $results = true;
                $this->closeConn();
                $_SESSION['User'] = serialize($this);
            }
            else {
                $results = false;
            }
            return $results;
        }

        /**
         * @param $string   String to change lastName to
         * @return bool Success
         */
        public function changeLastName($string) {
            /*
             * Perform SQL query and return boolean if success.
             */
            /*
            * Perform SQL query and return boolean if success.
            */
            $this->openConn();
            $sql = "UPDATE UserAccount SET lastName = :lastName WHERE uID = :uID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':lastName' => $string, ':uID' => $this->uID));

            if($stmt->rowCount() > 0) {
                $this->lastName = $string;
                $results = true;
                $this->closeConn();
                $_SESSION['User'] = serialize($this);
            }
            else {
                $results = false;
            }
            return $results;
        }

        /**
         * @param $string   String to change email to
         * @return bool Success
         */
        public function changeEmail($string) {
            /*
             * Perform SQL query and return boolean if success.
             */
            $this->openConn();
            $sql = "UPDATE UserAccount SET email = :email WHERE uID = :uID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':email' => $string, ':uID' => $this->uID));

            if($stmt->rowCount() > 0) {
                $this->email = $string;
                $results = true;
                $this->closeConn();
                $_SESSION['User'] = serialize($this);
            }
            else {
                $results = false;
            }
            return $results;
        }

        /**
         * @param $password
         * @param $newPassword
         * @return bool Success
         */
        public function changePassword($password, $newPassword) {
            /*
             * Perform SQL query and return boolean if success.
             */
            $this->openConn();
            $sql = "SELECT * FROM UserAccount WHERE uID = :uID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':uID' => $this->uID));

            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if(password_verify($password, $row['passHash'])) {
                    $sql = "UPDATE UserAccount SET passHash = :passHash WHERE uID = :uID";
                    $passHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute(array(':uID' => $this->uID, ':passHash' => $passHash));
                    $results = true;
                }
                else
                    $results = false;
            }
            else {
                $results = false;
            }

            $this->closeConn();
            return $results;
        }


        /** A function to display UserAccount info
         * @return string   String information of UserAccount
         */
        public function printInfo() {
            $results = $this->userName . ',' . $this->uID . ',' . $this->firstName . ',' . $this->lastName
                    . ',' . $this->email;

            return $results;
        }

        public function getUID() {
            return $this->uID;
        }

        public function deleteUserAccount($userName) {
            /*
             * Perform SQL query to delete UserAccount from DB
             */
            $this->openConn();

            $sql = "DELETE FROM UserAccount WHERE userName = :userName";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':userName'=>$userName));

            if($stmt->rowCount() > 0) {
                $results = true;
            }
            else {
                $results = false;
            }

            $this->closeConn();
            return $results;
        }

        /**
         * @return mixed
         */
        public function getUserName()
        {
            return $this->userName;
        }

        /**
         * @return mixed
         */
        public function getFirstName()
        {
            return $this->firstName;
        }

        /**
         * @return mixed
         */
        public function getLastName()
        {
            return $this->lastName;
        }

        /**
         * @return mixed
         */
        public function getEmail()
        {
            return $this->email;
        }

    }
