<?php

class StatusFile {

    private $path = __DIR__.'/../../lib/statusCode.ini';
    private $file = null;

    public function __construct() {
        $this->file = fopen($this->path, 'r+') or die("Unable to open file!");
    }

    public function __destruct() {
        fclose($this->file);
    }

    /** This function retreives the status code from the statusCode.ini file used for tracking the state of optimization
     * @return status code
     */
    public function getStatus() {
        $this->file = fopen($this->path, 'r+') or die("Unable to open file!");
        $line = "";
        $code = "";
        while(!feof($this->file)) {
            $line = fgets($this->file);
            $line = trim($line, " \t\n\r\0\x0B\xC2\xA0");
            if(substr($line, 0, 1 ) !== ";" && substr($line, 0, 1 ) !== "[") {
                $code = explode(" = ", $line);
                if (strcmp($code[0], "status") == 0) {
                    return $code[1];
                }
            }
        }
    }

    /** This function sets the status code of the file
     * @param $code
     */
    public function setStatus($code) {
        $old = "status = ".$this->getStatus();
        $new = "status = ".$code;
        $data = file_get_contents($this->path);
        $newdata = str_replace($old, $new, $data);
        file_put_contents($this->path, $newdata);
    }

}