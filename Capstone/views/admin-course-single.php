<?php
include("header.php");
if(!isset($_SESSION)) {
    session_start();
}?>
<link rel="stylesheet" type="text/css" href="css/master.css">
<link rel="stylesheet" type="text/css" href="css/table.css">
<link rel="stylesheet" type="text/css" href="css/course-list.css">

<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-course-single-controller.js"></script>

<div class="wrapper">
	<div class="inner_wrapper  course-list-wrapper in_grid">

		<div class="row " id="course-1">

			<div class="col-4">
				<h3><?php echo($_GET['subject'] . "  " . $_GET['courseNum'] . " - " . $_GET['session']);?></h3>
                <h3 hidden=TRUE id="hiddenSession"><?php echo($_GET['session']);?></h3>
                <h3 hidden=TRUE id="hiddencourseID"><?php echo($_GET['courseID']);?></h3>
			</div>
			<div class="col-8">
				<div class=" ta-info">
					<table class="lab-info-table course-table">
						<thead>
							<tr>
								<th>TA Name</th>
                                <th>Section</th>
								<th>GTA/UTA</th>
								<th>PREF</th>
								<th>PREP</th>
								<th>MARK</th>
								<th>OTHER</th>
								<th>MAX</th>
								<th>COURSE HOURS</th>
                                <th>TOTAL HOURS</th>
							</tr>
						</thead>
						<tbody>
						<script>numOfTAsInCourse(<?php echo($_GET['courseID'] . ',\'' . $_GET['session'] . "'");?>);</script><!-- This is called before the php code so we set the session variable below with the number of TAs in the given course -->
						</tbody>
					</table>
				</div>
				<div class=" container submit-container">
					<a class="button button-primary" id="updateTaButton">Update TA Hours</a>
				</div>

			</div>
		</div>
	</div>
</div>
