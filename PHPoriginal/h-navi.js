$(function(){

$("#hum-icon").on("click", function() {

  if ($("#hum-icon").hasClass("open")) {
    $("#hum-icon").removeClass("open");
    $("#hum-navi").hide(200);

  } else {
    $("#hum-icon").addClass("open");
    $("#hum-navi").show(200);

  }
});
});
