
<?php include("header.php");
ini_set('display_errors',1);
?>
<link rel="stylesheet" type="text/css" href="css/master.css">
<link rel="stylesheet" type="text/css" href="css/table.css">
<link rel="stylesheet" type="text/css" href="css/course-list.css">
<script type="text/javascript" src="../controllers/admin-ta-list-controller.js"></script>


<div class="wrapper">
	<div class="inner_wrapper edge_grid in_grid_large">

		<div class="row " id="uta-row">
			<div class="container">

					<div class="select-wrapper">
          		<select id="sessionSelection" name="sessionSelection">
              		<option value="W">Winter</option>
                  <option value="S">Summer</option>
									<!-- <option value="2018 W" selected>2019 Winter</option>
									<option value="2018 S">2019 Summer</option> -->
              </select>
							<?php // NOTE: add another filter that allows to filter TA's that are actually assigned to courses ?>
					</div>

					<h3>Showing all TAs assigned to a course</h3>

					<h2>UTAs</h2>

			</div>

			<button id="printbtn" type="submit" class="button" >Print Report</button>

			<table class="format-table ta-table" id="ta-table">

				<thead class="headings">
					<tr>
						<th>
							<h5>Email Offer</h5>
						</th>
						<th>
							<h5>TA Name + ID</h5>
						</th>
						<th>
							<h5>Year</h5>
						</th>
						<th>
							<h5>Program</h5>
						</th>
						<th>
							<h5>Assigned Courses + hours</h5>
						</th>
						<th>
							<h5>Pref</h5>
						</th>
						<th>
							<h5>Min</h5>
						</th>
						<th>
							<h5>Max</h5>
						</th>
						<th>
							<h5>Assigned</h5>
						</th>
						<th>
							<h5>Comments</h5>
						</th>
					</tr>

				</thead>

				<tbody id='content'>

        </tbody>

			</table>

			<button id="submituta" type="submit" class="button">Save Changes</button>
			<button id="emailOffers" type="button" class="button">Email Offer(s)</button>

		</div>



		<div class="row " id="gta-row">
			<div class="container">
				<h2>GTAs</h2>
			</div>
			<table class="format-table ta-table">

				<thead class="headings">
					<tr>
						<th>
							<h5>Email Offer</h5>
						</th>
						<th>
							<h5>TA Name + ID + Email</h5>
						</th>
						<th>
							<h5> Year</h5>
						</th>
						<th>
							<h5>Program</h5>
						</th>
						<th>
							<h5>Assigned Courses + hours</h5>
						</th>
						<th>
							<h5>Pref</h5>
						</th>
						<th>
							<h5>Min</h5>
						</th>
						<th>
							<h5>Max</h5>
						</th>
						<th>
							<h5>Assigned</h5>
						</th>
						<th>
							<h5>Comments</h5>
						</th>
					</tr>

				</thead>

				<tbody id='contentgta'>

				</tbody>

			</table>

			<button id="submitgta" type="submit" class="button">Save Changes</button>
			<button id="emailOffers2" type="button" class="button">Email Offer(s)</button>

		</div>

	</div>

</div>
