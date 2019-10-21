function loadTA(studentID) {
    $.ajax({
        type: 'post',
        url:    '../model/AdminFunction.php',
        data:   {functionCall: 'singleTAStudentInfo', studentID: studentID},
        success: function (data) {
            if(!data) {
             alert('Data is wrong in the function "loadTA" inside the file "single-ta-info-controller.js"');
            }
            //	If success
            else {
               //alert("data =" + data);

			   var info = data.split(',');
               var username = info[0];
               var firstName = info[1];
               var lastName = info[2];
               var studentNum = info[5];
               var email  = info[3];
               var statusText = info[4] == 0 ? "Undergraduate Student" : "Graduate Student";
               var status = info[4];
               var initials = firstName.charAt(0) +""+ lastName.charAt(0);
               $(".initials h4").html(initials);
				$('#ta-info h3').html(firstName + " " + lastName);
                $('#ta-info .username').html("Username: " +username);
                $('#ta-info .email').html(email);
                $('#ta-info .email').attr('href','mailto:'+email);
                $('#ta-info #status').html(statusText);

				document.getElementById("pref-hours").innerHTML = info[5];
				document.getElementById("min-hours").innerHTML = info[6];
				document.getElementById("max-hours").innerHTML = info[7];
				document.getElementById("lab-hours").innerHTML = info[8];

				//populating Currently Assigned Courses
				var index = 0;
				var index2 = 1;
				for(i = 0; i < info[info.length - 1] * 3; i = i + 3){//info[info.length - 1] is for getting the last value in the array, which is the number of rows we need to print the TA's Currently Assigned courses
					document.getElementById("Course" + ++index).innerHTML = info[i+9] + "-" + info[i+10] + "-" + info[i+11];
				}
				//populating Previous Assigned Courses
				var index3 = 0;
				for(i = 0; i < info[info.length - 2] * 3; i = i + 3){
					document.getElementById("prevTACourse" + ++index3).innerHTML = info[i+12] + "-" + info[i+13] + "-" + info[i+14];
				}

            }
        }
    })
}

function loadTAPrevCourseHistory(studentID) {
    $.ajax({
        type: 'post',
        url:    '../model/AdminFunction.php',
        data:   {functionCall: 'singleTAStudentPrevCourseHist', studentID: studentID},
        success: function (data) {
            if(!data) {
             alert('Data is wrong in the function "loadTAPrevCourseHistory" inside the file "single-ta-info-controller.js"');
            }
            //	If success
            else {
               //alert("data =" + data);
			   var info = data.split(',');

			   //Populating Previous Course History and Grades
			   var index2 = 1;
				for(i = 0; i < info[info.length - 1] * 5; i = i + 5){
					document.getElementById("PrevCourse" + index2).innerHTML = info[i] + " ---Session: \"" + info[i+1] + "\" ---Year: \"" + info[i+2] + "\"";
					document.getElementById("PrevGrade" + index2).innerHTML = info[i+3]+ " " + info[i+4];
					index2++;
				}

            }
        }
    })
}

function displayStudentInfo(){
	$.ajax({
		type: 'post',
		url:    '../model/UserHandler.php',
		data:   {functionCall: 'printInfo'},
		success: function (data) {
			//alert("grabStudentIDandPlaceItdata is=" + data);
			var info = data.split(',');
			var studentID = info[5];
			document.getElementById("student-num").innerHTML = studentID;
			loadTA(studentID);
			loadTAPrevCourseHistory(studentID);
		}
    })
}

function checkIfStudent(sID){
	$.ajax({
		type: 'post',
		url:    '../model/UserHandler.php',
		data:   {functionCall: 'getAccountType'},
		success: function (data) {
			//alert("DEBUG: checkIfStudent =" + data);
			if(data === "student"){//If a student is logged in, we will get his studentID and use display everything with that
				//alert("DEBUG: student hit");
				displayStudentInfo();
			}
			else if(data === "administrator"){
				//alert("DEBUG: admin hit, sID="+sID);
				document.getElementById("student-num").innerHTML = sID;//If an admin is logged in, we take the sID sent as a queryString argument in this function
				loadTA(sID);
				loadTAPrevCourseHistory(sID);
			}
		}
    })
}

$(document).ready(function() {
});
