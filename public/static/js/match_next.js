$(document).ready(function () {
	$(".next_match").click(function(){
	  var job2=$(this).val();
	  $.get("/index.php/index/interfacesadmin/match/job1/"+job1+"/job2/"+job2, function(result){
		if(result=="01")
		{
			alert(tip);
			window.location.href = "/index.php/index/admin/match";;
		}
		else
			$("#login_error").css('display','block');
		});
	});
});