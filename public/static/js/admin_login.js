function submit()
{
	var email=$("#email").val();
	var password=$("#password").val();
	$("#login_error").css('display','none');
	$("#login_freeze").css('display','none');
	$.get("/index.php/index/Interfacesadmin/login/email/"+email+"/password/"+password, function(result){
		if(result=="01")
			window.location.href = "/index.php/index/admin/match_list";
		else if(result=="00")
			$("#login_error").css('display','block');
		else
			$("#login_freeze").css('display','block');
  });
}