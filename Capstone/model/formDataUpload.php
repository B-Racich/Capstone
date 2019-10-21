<?php

class formDataObject {

  public $currentyear;
  public $gender;
  public $program;
  public $address;
  public $city;
  public $postcode;
  public $gta;
  public $prevta;
  public $prefhrs;
  public $maxhrs;

  public $TAs;


  //variable Functions (setters) - done individually to make updates easier, can change to one big function if more efficient
  function setCurrYr($currYr){
    $this->currentyear = $currYr;
  }

  function setGender($gender){
    $this->gender = $gender;
  }

  function setProgram($prog){
    $this->program = $prog;
  }

  function setAddress($address){
    $this->address = $address;
  }

  function setCity($city){
    $this->city = $city;
  }

  function setPostcode($pc){
    $this->postcode = $pc;
  }

  function setGta($gta){
    // //make gta a boolean value??
    // if($gta = "yes"){
    //   $boogta = true;
    // }else {
    //   $boogta = false;
    // }
    $this->gta = $gta;
  }

  function setPrevTa($prevta){
    $this->prevta = $prevta;
  }

  function setPrefHrs($prefhrs){
    $this->prefhrs = $prefhrs;
  }

  function setMaxHrs($maxhrs){
    $this->maxhrs = $maxhrs;
  }

  //creating form data object
  function formData(){
    // creating array
    $form = array("currentyear"=>$this->currentyear,"gender"=>$this->gender,"program"=>$this->program,"address"=>$this->address,"city"=>$this->city,"postalcode"=>$this->postcode,"gta"=>$this->gta,"previousta"=>$this->prevta,"prefhours"=>$this->prefhrs,"maxhours"=>$this->maxhrs);

    // creating a json object
    $myJSONform = json_encode($form);

    return $myJSONform;
  }

  function getPrefHours(){
    return $this->prefhrs;
  }

  function getMaxHours(){
    return $this->maxhrs;
  }

}

?>
