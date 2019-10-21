<?php
include_once('../model/SectionLoader.class.php');
include_once('../model/Course.class.php');
include_once('../model/Section.class.php');
include_once('../model/Professor.class.php');

if(!isset($_SESSION))
    session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload = $_FILES['CSVcourses'];
    $fname = $upload['name'];
    $fsize = $upload['size'];
    $fext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

    if($upload['error'] === UPLOAD_ERR_INI_SIZE) {
        error('File exceeds PHP\'s file upload size limit.');
    } else if($upload['error'] === UPLOAD_ERR_PARTIAL) {
        error('File did not fully upload.');
    } else if($upload['error'] === UPLOAD_ERR_NO_FILE) {
        error('No file was uploaded!');
    } else if($upload['error'] === UPLOAD_ERR_NO_TMP_DIR) {
        error('No temporary directory specified. Contact site administrator.');
    } else if($upload['error'] === UPLOAD_ERR_CANT_WRITE) {
        error('Unable to write uploaded file to disk.');
    } else if($upload['error'] === UPLOAD_ERR_OK) {
        if($fext != 'csv') {
            error('Incorrect file extension. File must be CSV.');
        } else if($upload['size'] > 20971520) {
            error('File exceeds 20 MB limit.');
        } else {
            if(move_uploaded_file($upload['tmp_name'], 'uploads/upload.csv')) {
                $SL = new SectionLoader('uploads/upload.csv');
                $ok = $SL->validateCourses();
                $_SESSION['uploadCourses'] = true;
                $_SESSION['objCourses'] = $SL->getCourses();
                unlink('uploads/upload.csv');
                if($ok) {
                    success('File has been uploaded!');
                    header('Location:reviewCourses.php');
                }
            } else {
                error('Unable to access uploaded file.');
            }
        }
    }
}

include_once('header.php');

echo '
<link rel="stylesheet" href="./css/form.css" />
<div class="form-wrapper" id="course-upload">
    <div class="form-title">
    <h1>Upload Course Data</h1>
    </div>
    <div class="form-body">
    <form action="" method="post" enctype="multipart/form-data">';
echo 'CSV file: <input type="file" name="CSVcourses" id="CSVcourses" accept=".csv">';
echo '<input type="submit" value="Upload" name="submit">';
echo '</form></div></div>
</div>';

?>