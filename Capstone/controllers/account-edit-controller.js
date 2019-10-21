function changePassword(curPass, newPass, retypePass) {
    //  If new passwords match
    if(newPass == retypePass) {
        $.ajax({
            type:   'post',
            url:    '../model/ta_edit_model.php',
            data:   {functionCall: 'changePassword', password: curPass, newPassword: newPass},
            success: function (results) {
                if(results) {
                    updateStatus("Password changed!");
                }
                else {
                    updateStatus("Could not change passwords, try again!");
                }
            }
        })
    }
    else {
        //Display error
        alert('Passwords do not match!');
    }
    $('#curPass').val('');
    $('#newPass').val('');
    $('#retypePass').val('');
}

function changeEmail(newEmail) {
    if(newEmail !== null) {
        $.ajax({
            type:   'post',
            url:    '../model/ta_edit_model.php',
            data:   {functionCall: 'changeEmail', string: newEmail},
            success: function (results) {
                if(results) {
                    $('#curEmail').text(newEmail);
                    updateStatus("Email changed!");
                }
                else {
                    $('#newEmail').val('');
                    updateStatus("Server error changing email! " + newEmail);
                }
                $('#newEmail').val('');
            }
        })
    }
}

function changeName(firstName, lastName) {
    if(firstName != null && firstName.length > 0) {
        $.ajax({
            type:   'post',
            url:    '../model/ta_edit_model.php',
            data:   {functionCall: 'changeFirstName', string: firstName},
            success: function (results) {
                if(results) {
                    $('#curFirstName').text(firstName);
                    updateStatus("First name changed!");
                }
                else {
                    updateStatus("Server error changing first name!");
                }
                $('#newFirstName').val('');
            }
        })
    }
    if(lastName != null && lastName.length > 0) {
        $.ajax({
            type:   'post',
            url:    '../model/ta_edit_model.php',
            data:   {functionCall: 'changeLastName', string: lastName},
            success: function (results) {
                if(results) {
                    $('#curLastName').text(lastName);
                    updateStatus("Last name changed!");
                }
                else {
                    updateStatus("Server error changing last name!");
                }
                $('#newLastName').val('');
            }
        })
    }
    else if((lastName == null || lastName.length > 0) && (firstName != null && firstName.length > 0)) {
        updateStatus("No data entered!");
    }
}

//Updates the current Names
function getNames() {
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'printInfo'},
        success: function (results) {
            arr = results.split(',');
            $('#curFirstName').text(arr[2]);
            $('#curLastName').text(arr[3]);
        }
    })
}

//Updates the current email
function getEmail() {
    $.ajax({
        type:   'post',
        url:    '../model/UserHandler.php',
        data:   {functionCall: 'printInfo'},
        success: function (results) {
            arr = results.split(',');
            $('#curEmail').text(arr[4]);
        }
    })
}

function showDiv(div) {
    switch(div) {
        case 'email':
            $('#changeEmailDiv').removeClass('hidden');
            $('#changeNameDiv').addClass('hidden');
            $('#changePasswordDiv').addClass('hidden');
            break;
        case 'name':
            $('#changeEmailDiv').addClass('hidden');
            $('#changeNameDiv').removeClass('hidden');
            $('#changePasswordDiv').addClass('hidden');
            break;
        case 'password':
            $('#changeEmailDiv').addClass('hidden');
            $('#changeNameDiv').addClass('hidden');
            $('#changePasswordDiv').removeClass('hidden');
            break;
    }

}

//  This is the main method
$(document).ready(function() {

    $('#formTitle').text("Change Email");
    showDiv('email');
    getEmail();

    //  Listeners for buttons
    $('#changePassword').click(function(e){
        e.preventDefault();
        $('#formTitle').text("Change Password");
        showDiv('password');
    });

    $('#changeEmail').click(function(e){
        e.preventDefault();
        $('#formTitle').text("Change Email");
        showDiv('email');
        getEmail();
    });

    $('#changeName').click(function(e){
        e.preventDefault();
        $('#formTitle').text("Change Name");
        showDiv('name');
        getNames();
    });

    //  Listeners for forms
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();
        var curPass = $('#curPass').val();
        var newPass = $('#newPass').val();
        var retypePass  = $('#retypePass').val();
        changePassword(curPass, newPass, retypePass);
    });

    $('#changeEmailForm').submit(function(e) {
        e.preventDefault();
        var newEmail = $('#newEmail').val();
        changeEmail(newEmail);
    });

    $('#changeNameForm').submit(function(e) {
        e.preventDefault();
        var firstName = $('#newFirstName').val();
        var lastName = $('#newLastName').val();
        changeName(firstName, lastName);
    });

});
