function checkIfAssigned(target, id, aID) {
    var uID = id;
    $.ajax({
        type:   'post',
        url:    '../model/admin_view_applications_model.php',
        data:   {functionCall: 'isAssigned', uID: uID},
        success: function (results) {
            if(results) {
                if(confirm("This TA has already been assigned courses, remove all assignments for this TA and decline?")) {
                    declineAndRemove(target, id, aID);
                }
            }
            else {
                declineStudent(id, aID)
                $(target).removeClass("button-default");
                $(target).addClass("button-disabled");
                $(target).closest("tr.ta-info-row").removeClass("undecided");
                $(target).closest("tr.ta-info-row").removeClass("hired");
                $(target).closest("tr.ta-info-row").addClass("declined");
            }
        }
    });
}

function acceptStudent(id, aID) {
    $.ajax({
        type:   'post',
        url:    '../model/admin_view_applications_model.php',
        data:   {functionCall: 'acceptStudent', id: id, aID: aID},
        success: function (results) {
            if(results == 'Student Accepted') {
                updateStatus(results);
                var ob = $('#'+aID+'-2');
                ob.removeClass("button-disabled");
                ob.addClass("button-default");
            }
            else {
                updateStatus(results);
            }
        }
    });
}

function declineAndRemove(target, id, aID) {
    $.ajax({
        type:   'post',
        url:    '../model/admin_view_applications_model.php',
        data:   {functionCall: 'removeAndDecline', id: id, aID: aID},
        success: function (results) {
            if(results == 'Student declined') {
                updateStatus(results);
                var ob = $('#'+aID+'-1');
                ob.removeClass("button-disabled");
                ob.addClass("button-default");
                $(target).removeClass("button-default");
                $(target).addClass("button-disabled");
                $(target).closest("tr.ta-info-row").removeClass("undecided");
                $(target).closest("tr.ta-info-row").removeClass("hired");
                $(target).closest("tr.ta-info-row").addClass("declined");
            }
            else {
                updateStatus(results);
            }
        }
    });
}

function declineStudent(id, aID) {
    $.ajax({
        type:   'post',
        url:    '../model/admin_view_applications_model.php',
        data:   {functionCall: 'declineStudent', id: id, aID: aID},
        success: function (results) {
            if(results == 'Student declined') {
                updateStatus(results);
                var ob = $('#'+aID+'-1');
                ob.removeClass("button-disabled");
                ob.addClass("button-default");
            }
            else {
                updateStatus(results);
            }
        }
    });
}

function listApplicants(sessionSelection) {
    $.ajax({
        type:   'post',
        url:    '../model/admin_view_applications_model.php',
        data:   {functionCall: 'listApplicants', sessionSelection: sessionSelection},
        success: function (results) {
            $('#content').html(results);

            // Add listeners to Accept button
            $("a[name*='acceptBtn-']").click(function() {
                if($(this).hasClass("button-default")) {
                    var arr = $(this).attr('name').split("-");
                    var id = arr[1];
                    var aID = arr[2]
                    acceptStudent(id, aID);
                    $(this).removeClass("button-default");
                    $(this).addClass("button-disabled");
                    $(this).closest("tr.ta-info-row").removeClass("undecided");
                    $(this).closest("tr.ta-info-row").removeClass("declined");
                    $(this).closest("tr.ta-info-row").addClass("hired");

                }
            });

            //  Add listeners to Decline button
            $("a[name*='declineBtn-']").click(function() {
                if($(this).hasClass("button-default")) {
                    var arr = $(this).attr('name').split("-");
                    var id = arr[1];
                    var aID = arr[2];
                    var target = $(this);
                    checkIfAssigned(target, id, aID);
                }
            });
        }
    });
}



$(document).ready(function() {

    listApplicants($("#sessionSelection").find(":selected").val());

    $("#sessionSelection").change(function() {
        var sessionSelection = $("#sessionSelection").find(":selected").val();
        listApplicants(sessionSelection);
    });

    // $('#yearSelection-wrapper').hide();

    $('#filterSelection').on('change', function() {
        var category = this.value;
        // alert(category);
        if(category == 'all'){
            $('.ta-info-row').each(function(){
                $(this).fadeIn();
            })
            $('#yearSelection-wrapper').hide();
            $('#progamSelection-wrapper').hide();
        }

        else if(category == 'program'){
            
            $('#yearSelection-wrapper').hide();
            // $('#filterSelection-wrapper').hide();
            $('#progamSelection-wrapper').show();
        }
        else if(category == 'year'){
            $('#filterSelection-wrapper').show();
            $('#progamSelection-wrapper').hide();
            $('#yearSelection-wrapper').show();
        }

      });

      $('#progamSelection').on('change', function() {
        var program = this.value;
        // alert(program);
        $('.ta-info-row').each(function(){
            $(this).fadeOut();
        })

        $('[class*='+program+']').each(function(){
            $(this).fadeIn();
        });

        $('[class*='+program+']').each(function(){
            $(this).fadeIn();
        });

        if(program == "other"){
            
        }
      });

      $('#yearSelection').on('change', function() {
        $('.ta-info-row').each(function(){
            $(this).fadeOut();
        })
        var year = this.value;
        // alert(year);
        if( year > 0){
           

            $('[class*=year'+year+']').each(function(){
                $(this).fadeIn();
            });
        }

        else{
            $('.GTA').each(function(){
                $(this).fadeIn();
            });
        }
    
        });

      
});