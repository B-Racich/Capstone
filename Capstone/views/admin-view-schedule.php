
<?php include("header.php"); 
ini_set('display_errors', 1);
?>
<link rel="stylesheet" type="text/css" href="css/master.css">
<link rel="stylesheet" type="text/css" href="css/table.css">

<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-schedule-controller.js"></script>

<div class="wrapper">
	<div class="inner_wrapper in_grid schedule-wrapper">
		<div class="row">
            <div id="main" style="width:100%;">
                    <p id="student-num" style="display:none;"></p>
            </div>

        </div>
	</div>

</div>
<script> checkIfStudent(<?php
if(isset($_GET['sID'])){
	echo($_GET['sID']);
}
?>)</script>