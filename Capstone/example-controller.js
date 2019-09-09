/*
	This is an example controller to call a function defined in UserHandler.php via the passed $_POST['functionCall']
	variable sent by AJAX along with its payload data.
 */


/*
	This is the basic layout for an AJAX call to a php file
 */
function loginStudent(userName, password) {	//	The parameters from the HTML
  $.ajax({
    type: 'post',	//	Type of request GET/POST
    url:  '../model/UserHandler.php',	//	path to file
	//	data is the $_POST[''] variables being sent to the php file
	data: {functionCall: 'loginStudent',userName: userName, password: password},
    success: function (data) {	//	the data being returned from the PHP function called
		/*
		This part is defined by what the expected data is from the php function called. In this case we are evaluating
		boolean returned.
		 */
    	//	If fail
		if(!data) {
			alert("Data false, inputs: " + userName + " ," + password);
			alert(data);
			$('#statusText').html("Fail");
		}
		//	If success
		else {
			alert("Data true, inputs: " + userName + " ," + password);
			alert(data);
			$('#statusText').html("Success");
			getInfo();
		}
    }
  })
}

/*
	This function makes a call to UserHandler.php and calls printInfo which returns the logged users information from
	the user stored in $_SESSION['User']
 */
function getInfo() {
  $.ajax({
    type: 'post',
    url:  '../model/UserHandler.php',
	data: {functionCall: 'printInfo'},	//	This function requires no additional data
    success: function (data) {	//	In this case the returned data is our string of information
		alert(data);
		$('#debug').html(data);	//	Updating the pages debug element with the data
    }
  })
}

// This is the main method of the controller, this runs on the page loading
$(document).ready(function() {
	
	// This listens for the HTML form submit
	$('#loginStudentForm').submit(function(e) {
		e.preventDefault();	//	prevent the form default action
		//	Call the AJAX loginStudent function and pass it the userName and password fields from the HTML form
		loginStudent($('#userNameField').val(), $('#passwordField').val());
	});
	
});