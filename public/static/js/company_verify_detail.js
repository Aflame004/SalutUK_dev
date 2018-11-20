$(document).ready(function () {
	$("#name").val(user_name).attr("disabled","disabled");
	$("#phone").val(phone).attr("disabled","disabled");
	$("#company_name").val(company_name).attr("disabled","disabled");
	$("#business").val(business).attr("disabled","disabled");
	$("#introduction").val(introduction).attr("disabled","disabled");
	$("#website").val(website).attr("disabled","disabled");
	
	$("#success").click(function(){
		$.get("/index.php/index/interfacesadmin/company_verify_success/company_id/"+company_id, function(result){
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
		$.get("/index.php/index/interfacesadmin/company_verify_fail/company_id/"+company_id+"/reason/"+reason, function(result){
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

