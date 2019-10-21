<?php

include_once __DIR__.'/../../public_html/model/Student.php';

class TA extends Student {

    protected $taID;
    protected $minhrs;
    protected $maxhrs;
    protected $comments;
	protected $labHours;
	protected $markingHours;
	protected $otherHours;
	protected $other2Hours;
    protected $isTA = false;
	
	protected $type = "ta";

    public function __construct() {
        parent::__construct();
    }

    public function getTAID(){
        return $this->taID;
    }

    public function isTA() {
        return $this->isTA;
    }

    public function printInfo() {
        $results = parent::printInfo();
        $results = $results . ',' . $this->taID . ','  . $this->minhrs . ','  . $this->comments;
        return $results;
    }

    public function createTA($minhrs, $maxhrs, $comments) {
        $this->openConn();
        $sql = "INSERT INTO TA (sID, minhrs, maxhrs, comments) VALUES(:sID, :minhrs, :maxhrs, :comments)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':sID'=>$this->sID, ':minhrs'=>$minhrs, ':maxhrs'=>$maxhrs, ':comments'=>$comments));

        if($stmt->rowCount() > 0) {
            $sql = "SELECT * FROM TA WHERE sID = :sID";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':sID'=>$this->sID));

            if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->taID = $row['taID'];
                $this->minhrs = $minhrs;
                $this->maxhrs = $maxhrs;
                $this->comments = $comments;
                $this->isTA = true;
                $results = true;
            }
        }
        else {
            $results = false;
        }

        $this->closeConn();
        return $results;
    }

    /** This function deletes the TA, Student, and UserAccount
     * @return bool
     */
    public function deleteTA() {
        $this->openConn();
        $sql = "DELETE FROM TA WHERE taID = :taID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':taID'=>$this->taID));

        if($stmt->rowCount() > 0) {
            $this->isTA = false;
            if($this->deleteStudent()) {
                $results = true;
            }
        }
        else {
            $results = false;
        }

        $this->closeConn();
        return $results;
    }

    public function loadTA($userName) {
        $this->openConn();
        $sql = "SELECT * FROM UserAccount, Student, TA WHERE userName = :userName AND UserAccount.uID = Student.uID AND Student.sID = TA.sID";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':userName' => $userName));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->userName = $row['userName'];
            $this->uID = $row['uID'];
            $this->firstName = $row['firstName'];
            $this->lastName = $row['lastName'];
            $this->email = $row['email'];
            $this->sID = $row['sID'];
            $this->graduate = $row['graduate'];
            $this->taID = $row['taID'];
            $this->minhrs = $row['minhrs'];
            $this->maxhrs = $row['maxhrs'];
            $this->comments = $row['comments'];
            $this->isStudent = true;
            $this->isTA = true;
            $results = true;
        }
        else {
            $results = false;
        }
        $this->closeConn();
        return $results;
    }

}