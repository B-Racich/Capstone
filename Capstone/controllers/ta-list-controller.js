function updateHoursandComment(id, minHours, maxHours, comment) {
    $.ajax({
        type:   'post',
        url:    '../model/admin_ta_list_model.php',
        data:   {functionCall: 'updateHoursandComment', id: id, minHours: minHours, maxHours: maxHours, comment: comment},
        success: function (results) {
            // if(results == true) {
            //     alert("TA id " + id + " Update passed");
            // }
            // else {
            //     alert("TA id " + id + " Update FAILED");
            // }
        }
    })
}

function listUTAs(sessionSelection) {
  $.ajax({
    type:   'post',
        url:    '../model/admin_ta_list_model.php',
        data:   {functionCall: 'listUTAs', sessionSelection: sessionSelection},
        success: function (results) {
            $('#content').html(results);
            // alert(results);
        }
    })
}

function listGTAs(sessionSelection) {
  $.ajax({
    type:   'post',
        url:    '../model/admin_ta_list_model.php',
        data:   {functionCall: 'listGTAs', sessionSelection: sessionSelection},
        success: function (results) {
            $('#contentgta').html(results);
            // alert(results);
        }
    })
}

// This is the main method of the controller, this runs on the page loading
$(document).ready(function() {

  listUTAs($("#sessionSelection").find(":selected").val());
  listGTAs($("#sessionSelection").find(":selected").val());

      $("#sessionSelection").change(function() {
          var sessionSelection = $("#sessionSelection").find(":selected").val();
          listUTAs(sessionSelection);
          listGTAs(sessionSelection);
      });

      //uta
      $('#submituta').click(function(){

        $("#utarow.ta-info-row").each(function(){

            var id = $(this).find('#utaname').attr('name');
            var minHours = $(this).find('#utamin').val();
            var maxHours = $(this).find('#utamax').val();
            var comment = $(this).find('#utacomment').val();
            // alert(id + " " + minHours + " " + maxHours + " " + comment);

            updateHoursandComment(id,minHours,maxHours,comment);
        })

        // alert("Um ...")

      });

      //gta
      $('#submitgta').click(function(){

        $("#gtarow.ta-info-row").each(function(){

            var id = $(this).find('#gtaname').attr('name');
            var minHours = $(this).find('#gtamin').val();
            var maxHours = $(this).find('#gtamax').val();
            var comment = $(this).find('#gtacomment').val();
            // alert(id + " " + minHours + " " + maxHours + " " + comment);

            updateHoursandComment(id,minHours,maxHours,comment);
        })

        // alert("Um ...")

      });

      $("#printbtn").click(function(){
          window.print();
      });

});
