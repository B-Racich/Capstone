function createStudent(userName, password, firstName, lastName, email, sID, graduate) {
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'createStudent', userName: userName, password: password, firstName: firstName, lastName: lastName, email: email, sID: sID, graduate: graduate},
        success: function (data) {
            if(data == true) {
                window.location.href = '../ta-portal.php';
            }
            else {
                console.log(data);
                updateStatus(data);
            }
        }
    })
}

function checkPasswords(pass1, pass2) {
    if(pass1 === pass2) {
        return true;
    }
    else {
        updateStatus("Passwords do not match");
        return false;
    }
}

$(document).ready(function() {

    // This listens for the HTML form submit
    $('#create-account').submit(function(e) {
        e.preventDefault();
        if(checkPasswords($('#password').val(), $('#confirm-pass').val())) {
            createStudent($('#username').val(), $('#password').val(), $('#fname').val(),$('#lname').val(),$('#email').val(),$('#sid').val(),0);
        }
    });

});
