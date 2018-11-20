function submit()
{
	var email=$("#email").val();
	$.get("/index.php/index/interfaces/forget_pwd?email="+email, function(result){
		if(result=="01")
			alert("请查收邮箱修改密码");
		else if(result=="00")
			alert("请确认邮箱是否存在");
		else
			alert("未知错误");
  });
}