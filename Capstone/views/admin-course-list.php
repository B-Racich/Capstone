<link rel="stylesheet" type="text/css" href="css/master.css">
<link rel="stylesheet" type="text/css" href="css/table.css">
<link rel="stylesheet" type="text/css" href="css/course-list.css">

<script type="text/javascript" src="../controllers/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../controllers/admin-course-list-controller.js"></script>
<?php include("header.php");
ini_set('display_errors', 1);

?>

<div class="box">
  <div class="b b1"></div>
  <div class="b b2"></div>
  <div class="b b3"></div>
  <div class="b b4"></div>
</div>

<div class="wrapper">
			
	<div class="inner_wrapper  course-list-wrapper in_grid">
        <div class="flex-row">
            <div>
                <div class="r-wrapper">
                    <p class="type"> Filter:   </p>
                    <div class="select-wrapper" id="filterSelection-wrapper">
                        <select id="filterSelection" name="filterSelection">
                            <option value="all">All</option>
                            <option value="program">Program</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                    <div class="select-wrapper" id="progamSelection-wrapper" style="display:none;">
                        <select id="progamSelection" name="progamSelection">
                            <option value="" disabled>-- Select one--</option>
                            <option value="COSC">COSC</option>
                            <option value="MATH">MATH</option>
                            <option value="DATA">DATA</option>
                            <option value="PHYS">PHYS</option>
                            <option value="other">OTHER</option>
                        </select>
                    </div>
                    <div class="select-wrapper" style="display:none;" id="yearSelection-wrapper">
                        <select id="yearSelection" name="yearSelection" >
                            <option value="" disabled>--Select one--</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4+</option>
                            <option value="-1">Graduate</option>
                        </select>
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
                <div class="r-wrapper">
                    <p class="type">Term: </p>
                    <div class="select-wrapper">
                        <select id="termSelection" name="termSelection">
                            <option value="1" selected>Term 1</option>
                            <option value="2">Term 2</option>
                        </select>
                    </div>
                </div>
            </div>
            <div>
                <form id="opt-form" method="post" action="../../deprecated-review/autoSched.php">
                    <div style="text-align:right;">
                        <h3>Automated Scheduler</h3>
                        Time Limit: <input type="text" name="tmLim" id="tmLim" value="45" /> seconds<br />
                        UTA hour budget: <input type="text" name="utahr" id="utahr" value="135" /><br />
                        GTA hour budget: <input type="text" name="gtahr" id="gtahr" value="100" /><br />
                        <button type="submit" class="button" name="autoSched">Run Automatic Scheduler</button>
                    </div>
                </form>
            </div>
        </div>
		<div class="row headings">
			<div class="col-3">
				<!-- <h2>Course Name</h2> -->
			</div>
			<div class="col-9">
				<!-- <h2>Lab Info</h2> -->
<!--                <input type="radio" name="taType" id="session" value="Recommended" class="apply" required> Recommend TAs-->
<!--                <input type="radio" name="taType" id="session" value="All" class="apply" checked> All available TAs-->
              
			</div>
		</div>
            <div class="" id="main">
                <!--  -->
            </div>
	</div>
</div>
