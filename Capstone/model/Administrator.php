<?php

include_once __DIR__.'/../../public_html/model/UserAccount.php';

class Administrator extends UserAccount {

    protected $aID;
    protected $isAdministrator = false;

    protected $type = "administrator";

    public function __construct() {
        parent::__construct();
    }

    public function getAID(){
        return $this->aID;
    }

    public function isAdministrator() {
        return $this->isAdministrator;
    }

    public function printInfo() {
        $results = parent::printInfo();
        $results = $results . ',' . $this->aID;
        return $results;
    }

    public function createAdministrator($userName, $password, $firstName, $lastName, $email) {
        $this->createUserAccount($userName, $password, $firstName, $lastName, $email);
        $this->openConn();
        $sql = "INSERT INTO Administrator (uID) VALUES(:uID)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':uID'=>$this->uID));

        if($stmt->rowCount() > 0) {
            $sql = "SELECT * FROM Administrator WHERE uID = :uID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':uID'=>$this->uID));

            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->isAdministrator = true;
                $this->aID = $row['aID'];
                $results = true;
            }
        }
        else {
            $results = false;
        }

        $this->closeConn();
        //$_SESSION['User'] = serialize($this);
        return $results;
    }

    public function editTaAccount($uID, $userName, $firstName, $lastName, $email, $graduate) {

        $this->openConn();

        $sql = "UPDATE UserAccount, Student
        SET UserAccount.userName = :userName, UserAccount.firstName = :firstName, UserAccount.lastName = :lastName, UserAccount.email = :email, Student.graduate = :graduate
        WHERE UserAccount.uID = :uID
        AND Student.uID = :uID"; //this goes first, uID never changes so this is fine

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':userName'=>$userName, ':firstName'=>$firstName, ':lastName'=>$lastName, ':email'=>$email, 'graduate'=>$graduate, ':uID'=>$uID, ':uID'=>$uID));

        if($stmt->rowCount() > 0) {
            $results = true;
        }
        else {
          $results = false;
        }

        $this->closeConn();

        return $results;
    }

    public function loginAdministrator($userName, $password) {
        /*
         * Perform SQL query to build a new UserAccount object from $uID
         */
        $this->openConn();
        $sql = "SELECT * FROM UserAccount, Administrator WHERE userName = :userName AND UserAccount.uID = Administrator.uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':userName'=> $userName));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //This function verifies the provided password against the stored hash returning true if matched
            if(password_verify($password, $row['passHash'])) {
                $this->userName = $row['userName'];
                $this->uID = $row['uID'];
                $this->firstName = $row['firstName'];
                $this->lastName = $row['lastName'];
                $this->email = $row['email'];
                $this->aID = $row['aID'];
                $this->isAdministrator = true;
                $results = "Login succeeded";
                $this->closeConn();
                $_SESSION['User'] = serialize($this);
                success('Thank you for logging in, '.$this->userName);
            }
            else {
                $this->closeConn();
                $results = "Invalid credentials";
            }
        }
        else {
            $this->closeConn();
            $results = "SQL failed";
        }
        $this->closeConn();
        return $results;
    }

    public function loadAdministrator($userName) {
        $this->openConn();
        $sql = "SELECT * FROM UserAccount, Administrator WHERE userName = :userName AND UserAccount.uID = Administrator.uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':userName' => $userName));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->userName = $row['userName'];
            $this->uID = $row['uID'];
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->email = $row['email'];
            $this->aID = $row['aID'];
            $this->isAdministrator = true;
            $results = true;
        }
        else {
            $results = false;
        }
        $this->closeConn();
        return $results;
    }

    /** This function deletes both the Administrator and base UserAccount from DB
     * @return bool
     */
    public function deleteAdministrator() {
        $this->openConn();
        $sql = "DELETE FROM Administrator WHERE uID = :uID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':uID'=>$this->uID));

        if($stmt->rowCount() > 0) {
            $this->isAdministrator = false;
            if($this->deleteUserAccount($this->userName)) {
                $results = true;
            }
        }
        else {
            $results = false;
        }

        $this->closeConn();
        return $results;
    }

}
