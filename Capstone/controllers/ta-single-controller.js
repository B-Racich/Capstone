var globalsID = "";

function loadTAsName(studentID) {
    $.ajax({
        type: 'post',
        url:    '../model/ta_single_model.php',
        data:   {functionCall: 'grabStudentName', studentID: studentID},
        success: function (data) {
            if(!data) {
             //alert('Data is wrong in the function "loadTAsName" inside the file "ta-single-controller.js"');
            }
            //	If success
            else {
               //alert("grabStudentName data =" + data);

                var info = data.split(',');
                var username = info[0];
                var firstName = info[1];
                var lastName = info[2];
                var studentNum = info[5];
                var email  = info[3];
                var statusText = info[4] == 0 ? "Undergraduate Student" : "Graduate Student";
                var status = info[4];
                var initials = firstName.charAt(0).toUpperCase() +""+ lastName.charAt(0).toUpperCase();
                $(".initials h4").html(initials);
                $('#ta-info h3').html(firstName + " " + lastName);
                $('#ta-info .username').html("Username: " +username);
                $('#ta-info .email').html(email);
                $('#ta-info .email').attr('href','mailto:'+email);
                $('#ta-info #status').html(statusText);
            }
        }
    })
}

function loadTAPrevCourseHistory(studentID) {
    $.ajax({
        type: 'post',
        url:    '../model/ta_single_model.php',
        data:   {functionCall: 'grabStudentPrevCourseHist', studentID: studentID},
        success: function (data) {
            if(!data) {
                // updateStatus('Data is wrong in the function "loadTAPrevCourseHistory" inside the file "single-ta-info-controller.js"');
            }
            //	If success
            else {
               // alert("grabStudentPrevCourseHist data =" + data);
			   var info = data.split(',');

			   //Populating Previous Course History and Grades
			   var index = 1;
				for(i = 0; i < info[info.length - 1] * 5; i = i + 5){
					document.getElementById("PrevCourse" + index).innerHTML = info[i] + " // Session: \"" + info[i+1] + "\" / Year: " + info[i+2];
					document.getElementById("PrevGrade" + index).innerHTML = info[i+3]+ " " + info[i+4];
					index++;
				}

            }
        }
    })
}

function loadTAHours(studentID,year, session) {
    $.ajax({
        type: 'post',
        url:    '../model/ta_single_model.php',
        data:   {functionCall: 'grabStudentHours', studentID: studentID, year: year, session: session},
        success: function (data) {
            if(!data) {
                // alert('Data is wrong in the function "loadTAHours" inside the file "single-ta-info-controller.js"');
                document.getElementById("pref-hours").innerHTML = 0;
                document.getElementById("min-hours").innerHTML = 0;
                document.getElementById("max-hours").innerHTML = 0;
                document.getElementById("lab-hours").innerHTML = 0;
            }
            //	If success
            else {
                //alert("loadTAHours data =" + data);
                var info = data.split(',');

                //Info comes in the order of pref, min, max, lab
                info[0] != "" ? document.getElementById("pref-hours").innerHTML = info[0] : document.getElementById("pref-hours").innerHTML = 0;
                info[1] != "" ? document.getElementById("min-hours").innerHTML = info[1] : document.getElementById("min-hours").innerHTML = 0;
                info[2] != "" ? document.getElementById("max-hours").innerHTML = info[2] : document.getElementById("max-hours").innerHTML = 0;
                info[3] != "" ? document.getElementById("lab-hours").innerHTML = info[3] : document.getElementById("lab-hours").innerHTML = 0;
            }
        }
    })
}

function loadTACurrentTAassignments(studentID){
	$.ajax({
        type: 'post',
        url:    '../model/ta_single_model.php',
        data:   {functionCall: 'grabStudentCurrentTAassignments', studentID: studentID},
        success: function (data) {
            if(!data) {
             // alert('Data is wrong in the function "loadTACurrentTAassignments" inside the file "single-ta-info-controller.js"');
            }
            //	If success
            else {
               //alert("loadTACurrentTAassignments data =" + data);

			   var info = data.split(',');

				//populating Currently Assigned Courses
				var index = 0;
				for(i = 0; i < info[info.length - 1] * 3; i = i + 3){//info[info.length - 1] is for getting the last value in the array, which is the number of rows we need to print the TA's Currently Assigned courses
					document.getElementById("Course" + ++index).innerHTML = info[i+0] + "-" + info[i+1] + "-" + info[i+2];
				}
            }
        }
    })
}

function loadTAPreviousTAassignments(studentID){
	$.ajax({
        type: 'post',
        url:    '../model/ta_single_model.php',
        data:   {functionCall: 'grabStudentPreviousTAassignments', studentID: studentID},
        success: function (data) {
            if(!data) {
             // alert('Data is wrong in the function "loadTAPreviousTAassignments" inside the file "single-ta-info-controller.js"');
            }
            //	If success
            else {
               //alert("loadTAPreviousTAassignments data =" + data);

			   var info = data.split(',');

				//populating Previous Assigned Courses
				var index = 0;
				for(i = 0; i < info[info.length - 1] * 3; i = i + 3){
					document.getElementById("prevTACourse" + ++index).innerHTML = info[i+0] + "-" + info[i+1] + "-" + info[i+2];
				}

            }
        }
    })
}

function displayStudentInfo(){
    var select = $("#sessionSelection").find(":selected").val();
    var year = select.split(" ")[0];
    var session = select.split(" ")[1];
	$.ajax({
		type: 'post',
		url:    '../model/UserHandler.php',
		data:   {functionCall: 'printInfo'},
		success: function (data) {
			var info = data.split(',');
			var studentID = info[5];
			document.getElementById("student-num").innerHTML = studentID;
			loadTAsName(studentID);
			loadTAPrevCourseHistory(studentID);
			loadTAHours(studentID, session, year);
			loadTACurrentTAassignments(studentID);
			loadTAPreviousTAassignments(studentID);
		}
    })
}

function checkIfStudent(sID){
    globalsID = sID; //Saving sID as a global variable so we can use it again.
    var select = $("#sessionSelection").find(":selected").val();
    var year = select.split(" ")[0];
    var session = select.split(" ")[1];
	$.ajax({
		type: 'post',
		url:    '../model/UserHandler.php',
		data:   {functionCall: 'getAccountType'},
		success: function (data) {
			//alert("DEBUG: checkIfStudent =" + data);
			if(data === "student"){//If a student is logged in, we will get his studentID and use display everything with that
				displayStudentInfo();//We have to go to another function to get his studentID
			}
			else if(data === "administrator"){
				//alert("DEBUG: admin hit, sID="+sID);
				document.getElementById("student-num").innerHTML = sID;//If an admin is logged in, we take the sID sent as a queryString argument in this function
				loadTAsName(sID);
				loadTAPrevCourseHistory(sID);
				loadTAHours(sID, year, session);
				loadTACurrentTAassignments(sID);
				loadTAPreviousTAassignments(sID);
			}
		}
    })
}

$(document).ready(function() {

  $("#printbtn").click(function(){
      window.print();
  });
    $("#sessionSelection").change(function () {
        checkIfStudent(globalsID);
    });
});
