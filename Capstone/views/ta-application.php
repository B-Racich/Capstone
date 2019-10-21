<?php include 'header.php';
?>

<link rel="stylesheet" href="css/form.css" />
<link rel="stylesheet" href="css/tabs.css" />

<script type="text/javascript" src="../controllers/ta-application-controller.js"></script>



<div class="wrapper" id="application">
    <!-- Things to consider - how to make sure only one application submitted per term -->
    <div class="">
        <div class="in_grid application-wrapper">
            <div class="main">
                <div class="form-wrapper">
                    <div class="align-center form-title">
                        <h2>Application for Winter 2019/20</h2>
                    </div>
                    <div class="form-body">
                        <form id="applicationForm" method="post" action="../model/Application.php" enctype="multipart/form-data" class="apply">
                            <!-- <h1>Submit TA Application</h1> -->
                            <div class="row">
                                <label for="session"><strong>Which session are you applying for? *</strong></label><br>
                                <input type="radio" name="session" id="session" value="2018 W" class="apply" required> 2018 Winter
                                <input type="radio" name="session" id="session" value="2018 S" class="apply"> 2018 Summer
                            </div>

                            <div class="row">
                                <div class="flex-item col-4">
                                    <label for="year"> Current Year of Studies:*</label><br>
                                        <div class="select-wrapper">
                                            <select name="year" required>
                                                <option value="year2">Year 2</option>
                                                <option value="year3">Year 3</option>
                                                <option value="year4">Year 4</option>
                                                <option value="year5">Year 5</option>
                                              </select>
                                        </div>
                                 </div>

                                <div class="flex-item col-4">
                                  <label for="gender">Gender:</label><br>
                                  <input type="radio" name="gender" id="gender" value="male" class="apply"> Male
                                  <input type="radio" name="gender" id="gender" value="female" class="apply"> Female
                                </div>

                                <div class="flex-item col-4">
                                   <label for="program">Program:*</label><br>
                                   <input type="text" class="apply" name="program" id="program" placeholder="" required="required" maxlength="50"/>
                                </div>
                            </div>

                            <div class="row">
                                <div class="flex-item col-4">
                                    <label for="address">Current Address:*</label><br>
                                    <input type="text" class="apply" name="address" id="address" placeholder="" required="required" maxlength="50"/>
                                </div>

                                <div class="flex-item col-4"><label for="city">City:*</label><br>
                                   <input type="text" class="apply" name="city" id="city" placeholder="" required="required" maxlength="50"/>
                                </div>

                                <div class="flex-item col-4">
                                   <label for="postal">Postal Code:*</label><br>
                                   <input type="text" class="apply" name="postal" id="postal" placeholder="" required="required" maxlength="6"/>
                                </div>
                            </div>

                            <div class="row">
                                <label for="gta">Will you be a full-time graduate student in the upcoming Winter 2019/20?*</label><br>
                                <!-- how can we have an easy way to update the wanted session? should we just type upcoming session? -->
                                <input type="radio" name="gta" id="gta" value="yes" class="apply" required> Yes
                                <input type="radio" name="gta" id="gta" value="no" class="apply"> No
                            </div>

                            <div class="row">
                                <label for="ta">Have you held a TA position before?*</label><br>
                                <input type="radio" name="ta" id="ta" value="yes" class="apply" required> Yes
                                <input type="radio" name="ta" id="ta" value="no" class="apply"> No
                            </div>

                            <p>Hours requested per week (Min:2 and Max:12)</p>

                            <div class="row">
                                <div class="col-4 flex-item">
                                    <label for="prefHours">Preferred hours:*</label><br>
                                    <input type="number" class="apply" name="prefHours" id="prefHours" required="required" maxlength="2" max="12" min="2" placeholder=""/>
                                </div>
                                <div class="col-4 flex-item">
                                    <label for="maxHours">Maximum hours:*</label><br>
                                    <input type="number" class="apply" name="maxHours" id="maxHours" required="required" maxlength="2" max="12" min="2" placeholder=""/>
                                </div>
                            </div>

                            <div class="row">
                                <div class = "col-6">I can't work these days/hours (Optional)</div>
                                <div class = "col-6">
                                <a href="#" class="modal-trigger button button-primary" data-modal="times">List Hours</a>
                                </div>
                               </div>
			
                            <div class="row">
                                <div class="modal" id="times">
                                    <div class="modal-sandbox"></div>
                                        <div class="modal-box">
                                            <div class="modal-header">
                                            <div class="close-modal">&#10006;</div> 
                                            <h1>Unavailable Times</h1>
                                            </div>
                                            <div class="modal-body">
                                            <table id="whenCantWorkTable">
                                    <tr>
                                        <td>
                                            <div class="row" id="time-1">
                                            I'm not available on &nbsp;
                                                <div class="select-wrapper">
                                                    <select name="selectweekday">
                                                        <option value="M">Monday</option>
                                                        <option value="T">Tuesday</option>
                                                        <option value="W">Wednesday</option>
                                                        <option value="R">Thursday</option>
                                                        <option value="F">Friday</option>
                                                    </select>
                                                </div>
                                                &nbsp;From&nbsp;
                                                <input type="time" name="from-time"></input>
                                                &nbsp;To&nbsp;
                                                <input type="time" name="to-time"></input>
                                                &nbsp;Or&nbsp;
                                                <input type="checkbox" name="allday" onclick="">All day <!-- this checkbox when checked should grey out the time inputs -->
                                            </div>
                                        </td>
                                    </tr>
                                </table>	
                                            <br />
                                            <button type="button" onclick="addRow()">List Another Time</button>

                                    </div>
                            </div>
                                </div>
                              		
                            </div>
                            
                            <div class="row " id="trans-upload">
                                <div class="row inner_row">
                                <label>Upload Files</label><br>
                                <p id="tip"><!-- Go to the SSC and download and copy your transcript into an excel sheet  -->                               
                                 <a href="#" class="modal-trigger button button-secondary help" data-modal="help"> Questions About Uploading?</a>
                               </p> </div>

                               <div class="modal" id="help">
                                    <div class="modal-sandbox"></div>
                                        <div class="modal-box">
                                            <div class="modal-header">
                                            <div class="close-modal">&#10006;</div> 
                                            <h1>Help</h1>
                                            </div>
                                            <div class="modal-body">
                                            <main>

<input id="tab1" type="radio" name="tabs" checked>
<label for="tab1">Transcript</label>

<input id="tab2" type="radio" name="tabs">
<label for="tab2">Schedule</label>


<section id="content1">
 <img src="assets/images/t1.jpeg">
 <img src="assets/images/t2.jpg">
 <img src="assets/images/t3.jpg">
</section>

<section id="content2">
<img src="assets/images/schedule1.jpg">
 <img src="assets/images/schedule2.jpg">
</section>


</main>
                                        
                                            </div>
                                     </div>
                                 </div>
</div>
                            <div class="row">
                                <div class="col-4 flex-item">
                                    <label for="transcript">Transcript*</label><br>
                                    <input type="file" name ="transcript" id="transcript" required='required' accept=".csv">
                                </div>
                                <div class="col-4 flex-item">
                                    <label for="schedule">Schedule*</label><br>
                                    <input type="file" name ="schedule" id="schedule" required='required' accept=".ics">
                                </div>
                            </div>

                            <div class="row">
                                <p id="tip">NOTE - International Students: Must provide a copy of Study Permit valid until April 30, 2020.</p>
                            </div>

                            <button type="submit" name = "submit" id="createbutton" class="button">Apply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="privacynotif">
            <div class="in_grid">
                <h4 class="">Privacy Notification </h4>
                <p class="text-block ">  Your personal information is collected under the authority of section 26(c) of the Freedom of Information and Protection of Privacy Act (FIPPA).
                This information will be used for general access and usage of the UBC Forms System. Questions about the collection of this information may be directed to <a href="mailto:itservices.ubco@ubc.ca">itservices.ubco@ubc.ca</a>.</p>
            </div>
        </div>
    </div>
</div>

<script>
var row = 2;
	function addRow(){
	    var parent = $("#whenCantWorkTable").find("tbody");
	    var html = "<tr><td><div class=\"row\" id=\"time-"+row+"\">\t\n" +
"                  I'm not available on &nbsp;\n" +
"                  <div class=\"select-wrapper\">\n" +
"                      <select name=\"selectweekday\">\n" +
"                           <option value=\"M\">Monday</option>\n" +
"                           <option value=\"T\">Tuesday</option>\n" +
"                           <option value=\"W\">Wednesday</option>\n" +
"                           <option value=\"R\">Thursday</option>\n" +
"                           <option value=\"F\">Friday</option>\n" +
"                      </select>\n" +
"                  </div>\n" +
"                  &nbsp;From&nbsp;\n" +
"                  <input type=\"time\" name=\"from-time\"></input>\n" +
"                  &nbsp;To&nbsp;\n" +
"                  <input type=\"time\" name=\"to-time\"></input>\n" +
"                  &nbsp;Or&nbsp;\n" +
"                  <input type=\"checkbox\" name=\"allday\" onclick=\"\">All day <!-- this checkbox when checked should grey out the time inputs -->\n" +
"                  </div></td></tr>";
		row++;
		parent.append(html);
	}
</script>

<script type="text/javascript" src="../controllers/common.js"></script>