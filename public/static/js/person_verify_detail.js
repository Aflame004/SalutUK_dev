$(document).ready(function () {
	$("#name").val(user_name).attr("disabled","disabled");
	$("#phone").val(phone).attr("disabled","disabled");
	$("#age").val(age).attr("disabled","disabled");
	$("#profile").val(profile_info).attr("disabled","disabled");
	$("#work_history").val(work_history).attr("disabled","disabled");

	
	$("#success").click(function(){
		$.get("/index.php/index/interfacesadmin/person_verify_success/id/"+user_id, function(result){
		if(result=="01")
		{
			window.location.href = document.referrer;
		}
		});
	});
	$("#fail").click(function(){
		var reason=$("#fail_reason").val();
		if(reason.length<=0)
		{
			$(".fail_reason").css('display','block');
			return;
		}
		$.get("/index.php/index/interfacesadmin/person_verify_fail/id/"+user_id+"/reason/"+reason, function(result){
		if(result=="01")
		{
			window.location.href = document.referrer;
		}
		});
	});
	$("#return").click(function(){
		window.location.href = document.referrer;
	});
});

