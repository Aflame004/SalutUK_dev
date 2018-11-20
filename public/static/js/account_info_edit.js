$(document).ready(function () {
	for(var i=18;i<=80;i++)
	{
		$("#age").append("<option value='"+i+"'>"+i+"</option>");
	}
	$('#name').val(user_name);
	$('#phone').val(phone);
	$('#age').val(age);
	$('#profile').val(profile_info);
	$('#work_history').val(work_history);
	$('#role').val(role);
});

function check_charnum_profile()
{
	var len=$("#profile").val().length;
	if(len>=500)
	{
		alert(len);
		$(".profile").css('display','block');
		return false;
	}
	else
	{	
		$(".profile").css('display','none');
		return true;
	}
}

function check_charnum_work_history()
{
	var len=$("#work_history").val().length;
	if(len>=500)
	{
		$(".work_history").css('display','block');
		return false;
	}
	else
	{
		$(".work_history").css('display','none');
		return true;
	}
}



function check_name()
{
	if($("#name").val().length<=0)
	{
		$(".name").css('display','block');
		return false;
	}
	else
	{
		$(".name").css('display','none');
		return true;
	}
}
function check_phone()
{
	if($("#phone").val().length<=0)
	{
		$(".phone").css('display','block');
		return false;
	}
	else
	{
		$(".phone").css('display','none');
		return true;
	}
		
}


function submit_check()
{
	
	if(check_charnum_profile()&&check_charnum_work_history()&&check_name()&&check_phone)
	{
		return true;
	}
	else
	{
		return false;
	}
}
