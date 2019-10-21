// Create administrator account
function createAdministrator(userName, password, firstName, lastName, email) {
  $.ajax({
    type: 'post',
    url:  '../model/UserHandler.php',
	//	Pass 1. function 2. userName 3. password
	data: {functionCall: 'createAdministrator',userName: userName, password: password, firstName: firstName, lastName: lastName, email: email},
    success: function (data) {
		//	If fail
		if(!data) {
			updateStatus("Server error, could not create account");
		}
		//	If success
		else {
			updateStatus("Admin account successfully created for " + userName);
			window.location.href = '../admin-portal.php';
		}
    }
  })
}

function checkPasswords(pass1, pass2) {
    if(pass1 === pass2) {
        //alert("Passwords match");
        return true;
    }
    else {
         updateStatus("Passwords do not match");
         //alert(pass1);
         //alert(pass2);
        return false;
    }
}

// This is the main method
$(document).ready(function() {
	// This listens for the HTML form submit
	$('#create-admin').submit(function(e) {
		e.preventDefault();
		if(checkPasswords($('#password').val(), $('#confirm-pass').val())) {
            createAdministrator($('#username').val(), $('#password').val(), $('#firstName').val(), $('#lastName').val(), $('#email').val());
        }
		
	});
});