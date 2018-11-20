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

function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return unescape(r[2]); return null; //返回参数值
}

function submit()
{
	var token = getUrlParam('token');
	var password=$("#password").val();
	$.get("/index.php/index/interfaces/change_by_token/token/"+token+"/password/"+password, function(result){
		if(result=="01")
			window.location = 'https://jobdemo.sprmint.cn';
		else if(result=="00")
			$("#login_error").css('display','block');
		else
			$("#login_freeze").css('display','block');
  });
}

