<?php 
ini_set('display_errors', 1);
include("header.php"); 
include_once __DIR__.'/../../lib/Database.class.php';
?>

<link rel="stylesheet" type="text/css" href="css/master.css">
<link rel="stylesheet" type="text/css" href="css/table.css">
<link rel="stylesheet" type="text/css" href="css/course-list.css">

<script type="text/javascript" src="../controllers/admin-view-applications-controller.js"></script>

<div class="wrapper">
	<div class="inner_wrapper edge_grid in_grid_large">
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
						<option value="BIO">BIO</option>
						<option value="COSC">COSC</option>
						<option value="MATH">MATH</option>
						<option value="PHYS">PHYS</option>
						<option value="PSYO">PSYO</option>
						<!-- <option value="other">OTHER</option> -->
                    </select>
				</div>
				<div class="select-wrapper" style="display:none;" id="yearSelection-wrapper">
                    <select id="yearSelection" name="yearSelection" >
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
	                        <option value="W">Winter</option>
	                        <option value="S">Summer</option>
	                    </select>
					</div>
				</div>
		<div class="row " id="gta-row">
			<div class="container">
				<h2>Students</h2>
			
			
			<table class="format-table hire-table">

				<thead class="headings">
					<tr>
						<th>
							<h5>TA Name + ID </h5>
						</th>
						<th>
							<h5> Year</h5>
						</th>
						<th>
							<h5>Program</h5>
						</th>
						<th>
							<h5> Email</h5>
						</th>
                        <th>
                            <h5>Transcript</h5>
                        </th>
                        <th>
                            <h5>Schedule</h5>
                        </th>
                        <th>
                            <h5>Status</h5>
                        </th>
						</th>					
					</tr>

				</thead>

				<tbody id='content'>

                </tbody>
			</table>

		</div>


    
		


		



	</div>

</div>


