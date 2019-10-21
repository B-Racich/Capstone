// Login to a student account
function loginAccount(userName, password) {
  $.ajax({
    type: 'post',
    url:  '../model/UserHandler.php',
	//	Pass 1. function 2. userName 3. password
	data: {functionCall: 'loginAccount',userName: userName, password: password},
    success: function (data) {
		//	If fail
		if(data == 'Login succeeded') {
            getAccountType();
		}
		//	If success
		else {
           updateStatus(data);
        }
    }
  })
}


//Determine account type and set header with elements
function getAccountType() {
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'getAccountType'},
        success: function (data) {
            switch(data.trim()) {
                case 'student':
                    window.location.href = '../ta-portal.php';
                    break;
                case 'administrator':
                    window.location.href = '../admin-portal.php';
                    break;
            }
        }
    })
}

// This is the main method
$(document).ready(function() {
	
	// This listens for the HTML form submit
	$('#loginForm').submit(function(e) {
		e.preventDefault();
		loginAccount($('#username').val(), $('#password').val());
	});
	
});