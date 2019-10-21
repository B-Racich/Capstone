<?php

include_once __DIR__.'/../../lib/Database.class.php';

class transcriptObject {

  private $conn;

  public $filename;
  public $file;
  public $csv;


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

  // file related functions

  function setFilename($filename){
    $this->filename = $filename;
  }

  function setFile($file){
    $this->file = $file;
  }

  function getFile(){
    return $this->$file;
  }

  function saveTranscript($file){
    // exploding the transcript line by line
    $csv = explode("\n", file_get_contents("$file"));

    foreach ($csv as $key => $line){
    	$csv[$key] = str_getcsv($line);
    }

    // creating a json object
    $myJSONcsv = json_encode($csv);

    // $this->$csv = $myJSONcsv; <- gettign an array to string conversion error

    return $myJSONcsv;
  }

  function printCompleteTranscript($csv) {
    print_r($csv);
  }

  function printGrades($sid) {
    //
    // $sID=$_SESSION['User']->getSID();
    //
    // $this->openConn();
    // $sql = "SELECT transcript FROM Application WHERE sID = $sID";
    // $stmt = $this->conn->prepare($sql);
    //
    // $stmt1->execute(array(':userName'=> $userName));
    //
    // if($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    //     $transcript = $row['transcript'];
    // }else {
    //     $transcript = null;
    // }
    //
    // $this->closeConn();
    //
    // $transcript = json_decode($transcript);
    // return $transcript;
  }

}

?>
