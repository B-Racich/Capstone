var state = -1;
var hasBuiltTable = false;

function checkStatus() {
    if(state != 200) {
        $.ajax({
            type:   'post',
            url:    '../model/admin_review_schedule_model.php',
            data:   {functionCall: 'checkStatus'},
            success: function (code) {
                code_before = code.split(", ")[0];
                code_after = code.split(", ")[1];
                console.log("Code before: " + code_before + "\tCode after: " + code_after);
                state = code_after;
                if(state == 302) {
                    updateStatus("Scheduler error reading constraint data")
                    setTimeout(checkStatus, 10000); // The interval set to 10 seconds
                }
                else if(state == 301) {
                    updateStatus("Infeasible schedule with current assignments")
                    setTimeout(checkStatus, 10000); // The interval set to 10 seconds
                }
                else if(state == 300) {
                    updateStatus("Scheduler running...");
                    setTimeout(checkStatus, 10000); // The interval set to 5 seconds
                }
                else if(state == 200) {
                    updateStatus("Found schedule data, importing...")
                    updateTable();
                }
                else if(state == 100) {
                    updateStatus("Scheduler idle, checking for schedule data...");
                    updateTable();
                    setTimeout(checkStatus, 30000); // The interval set to 30 seconds
                }
            }
        });
    }
};

//  This function appends a row to the table
function addRow(section, ta, prepHours, markingHours, otherHours, other2Hours) {
    var $table = $("#reviewTable");

}

function updateTable() {
    var $table = $("#reviewTable");
    $.ajax({
        type:   'post',
        url:    '../model/admin_review_schedule_model.php',
        data:   {functionCall: 'getTableData'},
        success: function (html) {
            $table.find("tr:gt(0)").remove();
            $table.append(html);
        }
    });
}

function assignTAs() {
    $.ajax({
        type:   'post',
        url:    '../model/admin_review_schedule_model.php',
        data:   {functionCall: 'assignTAs'},
        success: function (state) {
            console.log(state);
            if(state == 0) {
                updateStatus('TAs have been scheduled');
            }else if(state > 0) {
                updateStatus(state+' assignments were unable to be scheduled due to existing conflicts in the DB');
            }else if(state == -1) {
                updateStatus("SQL failed");
            }
        }
    });
}

//  This is the main method
$(document).ready(function() {

    state = checkStatus();

    $("#confirmBtn").click(function() {
       if(state == 100) {
           if(confirm("Do you want to carry out this schedule assignment?")) {
               assignTAs();
           }
       }else{
           updateStatus("No optimization data present");
       }
    });
    $("#cancleBtn").click(function() {
        updateStatus("Redirecting...");
        window.setTimeout(function(){
            window.location.href = "../ta-portal.php";
        }, 2000);
    });

});
