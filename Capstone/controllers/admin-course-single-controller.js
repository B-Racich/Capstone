var numOfTAsListed = 0;
var previousTAHours = [];
var dataSplit;

function listCourse(course, session) {
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/admin_course_single_model.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'listCourse',course: course, session: session},
    success: function (data) {
		if(!data) {
			updateStatus("There are not any TA's for this course");
		}
		else {	//display course data
			var count = 0;
            // alert("DEBUG:ajax success. courseID=" + course);
			// alert("DEBUG:DATA: " + data + " END DATA DEBUG");
			dataSplit = data.split(','); //Splitting retrieved info by comma

			//Populating TA's info into course-single.php
			for (var i = 0; i < dataSplit.length-1; i = i + 9) { //dataSplit.length -1 because the dataSplit arrays last element is just a comma
                document.getElementById("name" + count).innerHTML = dataSplit[i];
                document.getElementById("section" + count).innerHTML = dataSplit[i+1];
				document.getElementById("gtaUta" + count).innerHTML = (dataSplit[i+2] == 1? "GTA": "UTA");
				document.getElementById("pref" + count).innerHTML = (dataSplit[i+3] == ""? "N/A" : dataSplit[i+3]);
				document.getElementById("prep" + count).value = dataSplit[i+4];
				document.getElementById("mark" + count).value = dataSplit[i+5];
				document.getElementById("other" + count).value = dataSplit[i+6];
				document.getElementById("max" + count).innerHTML = dataSplit[i+7];
				document.getElementById("email" + count).innerHTML = dataSplit[i+8];

                //Now we'll store these current values we're populating the course list to serve as a "snapshot" of what the hours were before they've been altered
                previousTAHours[i] = dataSplit[i+4];//grabbing the "prep" hours
                previousTAHours[i+1] = dataSplit[i+5];//grabbing the "mark" hours
                previousTAHours[i+2] = dataSplit[i+6];//grabbing the "other" hours
                previousTAHours[i+3] = dataSplit[i+8];//updateTAsHours uses email to update their hours
                previousTAHours[i+4] = dataSplit[i+1];//grabbing the sectionID
				count++;
			}
            populateCourseHours(dataSplit, session);
            populateTotalHours(dataSplit, session);
		}
    }
  })
}

function populateCourseHours(courseInfo, session) {
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/admin_course_single_model.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'populateCourseHours',courseInfo: courseInfo, session: session, courseID: document.getElementById("hiddencourseID").innerHTML},
    success: function (data) {
		if(!data) {
			updateStatus("Error populating Course Hours");
		}
		else {
            // alert(data);
            dataSplit = data.split(','); //Splitting retrieved info by comma
            for (var i = 0; i < dataSplit.length-1; i++) { //dataSplit.length -1 because the dataSplit arrays last element is just a comma
                document.getElementById("courseHRS" + i).innerHTML = dataSplit[i];
            }
		}
    }
  })
}

function populateTotalHours(courseInfo, session) {
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/admin_course_single_model.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'populateTotalHours',courseInfo: courseInfo, session: session},
    success: function (data) {
		if(!data) {
			updateStatus("Error populating Total Hours");
		}
		else {
			//	If success
            // alert(data);
            dataSplit = data.split(','); //Splitting retrieved info by comma
            for (var i = 0; i < dataSplit.length-1; i++) { //dataSplit.length -1 because the dataSplit arrays last element is just a comma
                document.getElementById("totalHRS" + i).innerHTML = dataSplit[i];
            }
		}
    }
  })
}

function updateTAsHours(prevTAHours) {
	//need to take all of the potentially altered "prep" "mark" and "other" hours to see if they've changed
	var alteredTAHours = [];
	var count = 0;
	for(i = 0; i < numOfTAsListed * 5; i = i + 5){
		alteredTAHours[i] = document.getElementById("prep"+ count).value;
		alteredTAHours[i+1] = document.getElementById("mark"+ count).value;
		alteredTAHours[i+2] = document.getElementById("other"+ count).value;
		alteredTAHours[i+3] = document.getElementById("email"+ count).innerHTML;
        alteredTAHours[i+4] = document.getElementById("section"+ count).innerHTML;
        if(!Number.isInteger(Number(alteredTAHours[i])) || !Number.isInteger(Number(alteredTAHours[i+1])) || !Number.isInteger(Number(alteredTAHours[i+2]))){
            updateStatus("Error, Non integer value found in one of the fields");
            return;
        }
        else if(alteredTAHours[i] > 2){
            updateStatus("Error, cannot have more than 2 prep Hours");
            return;
        }
		count++;
	}
    // alert("debug: prevTAHours=" + prevTAHours + "alteredHours" + alteredTAHours);
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/admin_course_single_model.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'updateTAsHours', prevTAHours: prevTAHours, alteredTAHours: alteredTAHours},
    success: function (data) {
		if(!data) {
			updateStatus("Data false");
		}
		//	If success
		else {
			// alert("Data true");
			// alert(data);
            updateStatus("TA's successfully updated!");
			location.reload();
		}
    }
  })
}

function numOfTAsInCourse(course,session){
	//alert("DEBUG:numofTAsInCourse course = " + course);
	$.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/admin_course_single_model.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'numOfTAsInCourse',course: course, session: session},
    success: function (data) {
		if(!data) {
            updateStatus("No TA's in course");
		}
		//	If success
		else {
			// alert("success, numOfTAsInCourse data= " + data);
			numOfTAsListed = data;//Other methods in this page could use this information so im storing it in a global variable
            populateRows(numOfTAsListed);
		}
    }
  })
}

function populateRows(numOfTAsListed){
    for(var i = 0; i < numOfTAsListed; i++){//This for loop is populating setting the number of rows based on how many TA's we counted from our session variable
        $("tbody").append('<tr class="ta-row">');//Also there are 9 labels because we have a hidden last column which stores the email which will be used for emailing the TA's with their offer
        $("tbody").append('<td> <label id="name' + i + '"></label></td>');
        $("tbody").append('<td> <label id="section' + i + '"></label></td>');
        $("tbody").append('<td> <label id="gtaUta' + i + '"></label></td>');
        $("tbody").append('<td> <label id="pref' + i + '"></td>');
        $("tbody").append('<td> <input type="text" size="1" id="prep' + i + '"></td>');
        $("tbody").append('<td> <input type="text" size="1" id="mark' + i + '"></td>');
        $("tbody").append('<td> <input type="text" size="1" id="other' + i + '"></td>');
        $("tbody").append('<td> <label id="max' + i + '"></label></td>');
        $("tbody").append('<td> <label id="courseHRS' + i + '"></label></td>');
        $("tbody").append('<td> <label id="totalHRS' + i + '"></label></td>');
        $("tbody").append('<td> <label id="email' + i + '" hidden="TRUE"></label></td>'); //This column stores the email for the TA, however it is not visible
        $("tbody").append('</tr>');
    }
}

$(document).ready(function() {
    var session = document.getElementById("hiddenSession").innerHTML;//Grabbing the sessionYear and sessionSeason from the page
    var courseID = document.getElementById("hiddencourseID").innerHTML;//Grabbing courseID from the page
    listCourse(courseID,session);//We first had to find the number of TA's in the course and build the table, we can now list the TA's and put them into the table now

	document.getElementById("updateTaButton").addEventListener("click", function(){
		updateTAsHours(previousTAHours);
	});
});
