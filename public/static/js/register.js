function check_email(){
  var reg = /\w+[@]{1}\w+[.]\w+/;
  if (!reg.test($("#email").val())){
    $("#email").focus();
	$(".span_email").css('display','block');
	return false;
  }
  else
  {
	$(".span_email").css('display','none');
	return true;
  }
}

function check_consistency(){
	var a=$("#password").val();
	var b=$("#password_again").val()
	if(a!=b)
	{
		$(".span_password_again").css('display','block');
		return false;
		
	}
	else
	{
		$(".span_password_again").css('display','none');
		return true;
	}
}

function check_password(){
	if($("#password").val().length>=6)
	{
		$(".span_password").css('display','none');
		return true;
	}
	else
	{
		$(".span_password").css('display','block');
		return false;
	}
}

function submit_check()
{
	if(check_email()&&check_consistency()&&check_password())
	{
		return true;
	}
	return false;
}


$(document).ready(function () {
	if(email_exist==1)
	{
		$("#email_exist").css('display','block');
	}
});
