$(document).ready(function () {
	$(".complete").click(function(){
	  var id=$(this).val();
	  $.get("/index.php/index/interfacesadmin/match_complete/id/"+id, function(result){
		if(result=="01")
		{
			window.location.reload();
		}
		});
	});
	$(".cancel").click(function(){
	  var id=$(this).val();
	  $.get("/index.php/index/interfacesadmin/match_cancel/id/"+id, function(result){
		if(result=="01")
		{
			window.location.reload();
		}
		});
	});
});