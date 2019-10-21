<?php

include_once __DIR__.'/../../lib/Database.class.php';
include_once __DIR__.'/../../public_html/model/UserAccount.php';
include_once __DIR__.'/../../public_html/model/Student.php';
include_once __DIR__.'/../../public_html/model/Administrator.php';
include_once __DIR__.'/../../public_html/model/TA.php';
include_once __DIR__.'/../../public_html/model/Section.class.php';
error_reporting(E_ALL); ini_set('display_errors', 1);

if(!isset($_SESSION)) {
    session_start();
}

//  Get the POST functionCall variable that is to be called
$functionCall = $_POST['functionCall'];

//  Create a new AdminFunction object
$ob = new forgot_password_model($functionCall);

//  Run the function
$ob->callFunction();

//  Return to ajax
$ob->echoResults();

class forgot_password_model {

    private $functionCall;  //  function called
    private $results;   //  output from function

    public function __construct($functionCall) {
        $this->functionCall = $functionCall;
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

    public function getFunctionCall() {
        return $this->functionCall;
    }

    public function echoResults() {
        echo $this->results;
    }

    public function callFunction()
    {
        switch ($this->functionCall) {
            case    'sendReset':
                $this->results = $this->sendReset();
                break;
            case    'resetPassword':
                $this->results = $this->resetPassword();
                break;
        }
    }



    /** Send reset email to user Email
     * Return whether it is a success or not (true or false)
     */
    private function sendReset() {
      $email = $_POST['email'];
      $userName = $_POST['username'];

      $this->openConn();

      $sql = "SELECT firstName FROM UserAccount WHERE email = :email and userName = :userName";
      $stmt = $this->conn->prepare($sql);
      $stmt->execute(array(':email'=> $email, ':userName'=> $userName));

      $emailexists = false;

      if($stmt->fetch(PDO::FETCH_ASSOC)) {
          $emailexists = true;

          $firstName = $row['firstName'];
      }

      if($emailexists) {
        // Create the unique user password reset key
        $salt = "498#2D83B631%3800EBD!801600D*7E3CC13";
        $key = hash('sha512', $salt.$email);
        $pwurl = "https://ta-scheduler.com/reset-password.php?check=".$key;
        $to = $email;
        $subject = "TA Allocator - Reset Password";
        $txt = "Dear $firstName (username: $userName),\n\nIf this e-mail does not apply to you please ignore it. It appears that you have requested a password reset to your account on the TA Allocator website.\n\nTo reset your password, please click the link below. If you cannot click it, please paste it into your web browser's address bar.\n\n" . $pwurl . "\n\nThanks,\nThe Administration";
        $headers = "From: puppylovewow@gmail.com"; //change to admins email later

        // have to put on server to test this
        mail($to,$subject,$txt,$headers);

        $this->closeConn();
        return true;
      }else {
        $this->closeConn();
        return false;
      }


    }


    /** Reset Password based on the link user clicked
     * Return whether it is a success or not (true or false)
     */
    private function resetPassword() {

      // the email and username they entered
      $email = $_POST['email'];
      $username = $_POST['username'];

      // the confirmation from the reset link
      $check = $_POST['check'];

      // Use the same salt from the forgot_password.php file
      $salt = "498#2D83B631%3800EBD!801600D*7E3CC13";
      // Generate the reset key
      $resetkey = hash('sha512', $salt.$email);

      // new Password
      $password = $_POST['password'];
      $passHash = password_hash($password, PASSWORD_DEFAULT);

      $this->openConn();

      // Does the new reset key match the old one?
      if ($resetkey == $check) {
        $sql = "UPDATE UserAccount SET passHash = :passHash WHERE email = :email and userName = :userName";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array('passHash'=> $passHash, ':email'=> $email, 'userName'=> $username));


        if($stmt->rowCount() > 0) {
            $this->closeConn();
            return true;
        }
        else {
            $this->closeConn();
            return false;
        }

      }else{
        $this->closeConn();
        return false;
      }

    }

  }
