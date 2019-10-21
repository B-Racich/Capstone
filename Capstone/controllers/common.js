/** Updates the status text on the page
 *
 * @param text
 */
function updateStatus(text) {
    $("#statusText").text(text);

    if($(document).scrollTop()==0){
        $("#statusText").css("position","unset");
    }
    else if($(document).scrollTop()>0){
        $("#statusText").css("position","fixed");
    }

    $("#statusText").removeClass("hidden");
    setTimeout(function () {
        $('#statusText').addClass("hidden");
    }, 800);
}

function urlParam(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
        return null;
    }
    else{
        return results[1] || 0;
    }
}

$(".modal-trigger").click(function(e){
  e.preventDefault();
  dataModal = $(this).attr("data-modal");
  console.log(dataModal);
  $("#" + dataModal).css({"display":"block"});
  // $("body").css({"overflow-y": "hidden"}); //Prevent double scrollbar.
});

$(".close-modal, .modal-sandbox").click(function(){
  $(".modal").css({"display":"none"});
  // $("body").css({"overflow-y": "auto"}); //Prevent double scrollbar.
});