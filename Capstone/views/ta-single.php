
<?php include("header.php"); ?>
<link rel="stylesheet" type="text/css" href="css/single-ta.css">
<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/ta-single-controller.js"></script>

<div class="wrapper">
	<div class="inner_wrapper in_grid single-ta-wrapper">
		<div class="row">
			<div id="test"></div>
			<div class="row inner_row col-8">
				<div class="col-2 flex-item align-center" id="inital-print">
					<div class="initials circle align-center">
						<h4></h4>
					</div>
					<button id="printbtn" type="submit" class="button button-primary">Print Report</button>
					<?php
						if(unserialize($_SESSION['User'])->getAccountType() == "student")
							echo('<a href="../../deprecated-review/account-edit.php" class="button button-primary">Edit Info</a>');
					?>
				</div>
				<div class="col-6 flex-item">
					<div id="ta-info" class="text-block">
						<h3 id="fname-lname"></h3>
						<label id="student-num" ></label>
						<h4 id="status"></h4>
						<p class="username"></p>
						<a href="" class="email"></a>
					</div>
				</div>
				<div class="r-wrapper">
				<p class="type">Session: </p>
					<div class="select-wrapper">
						<select id="sessionSelection" name="sessionSelection">
							<option value="2018 W" selected>2019 Winter</option>
							<option value="2018 S">2019 Summer</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-4 flex-item">
				<h3>Currently Assigned Courses</h3>
					<div id="current_courses" class="course-wrapper scrollable courses scrollable-wrapper">
						<ul class="course-list">
							<li class="course-item" id="Course1">-</li>
							<li class="course-item" id="Course2">-</li>
							<li class="course-item" id="Course3">-</li>
							<li class="course-item" id="Course4">-</li>
							<li class="course-item" id="Course5">-</li>
							<li class="course-item" id="Course6">-</li>
							<li class="course-item" id="Course7">-</li>
							<li class="course-item" id="Course8">-</li>
						</ul>
					</div>
			</div>
	</div>
	<div class="row">
		<div class="col-8">
			<h3>Previous Course History and Grades</h3>
			<div id="prev_grades" class=" scrollable grades scrollable-wrapper">
				<ul class="grades-list">
					<?php
						for($i = 0; $i < 45; $i++){
							echo '<li class="grade-item clear-floats"><span class="left-float" id="PrevCourse' . ($i+1) . '"> </span><span class="right-float" id="PrevGrade' . ($i+1) . '"></span></li>';
						}
					?>
				</ul>
			</div>
		</div>
		<div class="col-4">
				<h3>Previous TA Assignments</h3>
			<div id="prev_ta_assign" class=" scrollable assign scrollable-wrapper">
				<ul class="assign-list">
					<?php
					for($i = 0; $i < 10; $i++){
						echo '<li class="assign-item" id="prevTACourse' . ($i+1) . '">-</li>';
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	<div class="row hour-assign-row">
		<div class="col-8">
			<div class="row inner_row">
				<div class="col-4 hour-assign flex-item ">
					<div class="assign-wrapper square-wrapper align-center">
						<div class="square">
							<p id="lab-hours"></p>
						</div>
						<h5>Currently Assigned Hours</h5>

					</div>
				</div>
				<div class="col-4 hour-assign flex-item">
					<div class="assign-wrapper square-wrapper align-center">
						<div class="square">
							<p id="pref-hours"></p>
						</div>
					<h5>Preferred Hours</h5>
					</div>
				</div>
				<div class="col-4 hour-assign flex-item">
					<div class="assign-wrapper square-wrapper align-center">
						<div class="square">
							<p id="min-hours"></p>
						</div>
						<h5>Min Hours</h5>
					</div>
				</div>
				<div class="col-4 hour-assign flex-item">
					<div class="assign-wrapper square-wrapper align-center">
						<div class="square">
							<p id="max-hours"></p>
						</div>
						<h5>Max Hours</h5>
					</div>
				</div>
			</div>
		</div>
		<div class="col-4">

		</div>
	</div>


</div>
<script> checkIfStudent(<?php
if(isset($_GET['sID'])){
	echo($_GET['sID']);
}
?>)</script>
