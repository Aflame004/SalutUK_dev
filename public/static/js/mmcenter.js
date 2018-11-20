$(document).ready(function () {
	for(var i=18;i<=80;i++)
	{
		$("#age").append("<option value='"+i+"'>"+i+"</option>");
	}
	$("#create_compay").click(function(){
        //$("#addModalLabel").text("编辑");
        $('#addModal').modal();
    });
	$("#add_company_submit").click(function(){
;
        var company_name=$('#add_company_name').val();
        var business=$('#add_company_business').val();
        var introduction = $('#add_company_introduction').val();
        var website=$('#add_company_website').val();
		$.post("/index.php/index/interfaces/add_company/company_name/", 
			{ 
				company_name: company_name,
				business: business,
				introduction: introduction,
				website: website
			},
			function(data){
			if(data=="01"){
                     document.location.reload();
             }
		 });
     })
	 $(".del_company").click(function(){
		var company_id=$(this).val();
		$.get("/index.php/index/interfaces/del_company/company_id/"+company_id,function(data){
            if(data=="01"){
                  document.location.reload();
            }
        });
	 });
	 
	 //如果已提交个人信息，自动填写
	 if(info_exist)
	 {
		 if(status==0||status==1)//审核中或者审核成功后不得修改内容
		 {	 
			 $('#name').val(user_name).attr("disabled","disabled");
			 $('#phone').val(phone).attr("disabled","disabled");
			 $('#age').val(age).attr("disabled","disabled");
			 $('#profile').val(profile).attr("disabled","disabled");
			 $('#work_history').val(work_history).attr("disabled","disabled");
			 $('#btn_personal_submit').remove();
		 }
		else if(status==2)//审核失败后
		{
			$('#phone').val(phone);
			 $('#age').val(age);
			 $('#profile').val(profile);
			 $('#work_history').val(work_history);
		}
	 }
	 //邮箱验证提交与倒计时
	 $('#btn_send_email').click(function(){
		 var second=60;
		 $("#btn_send_email").attr("disabled", true);
		 $.get("/index.php/index/interfaces/send_email/type/1", function(result){
			 
		});
		clock(second);
			
	 });
	 
	 $('#btn_change_password').click(function(){
		 var origin_password=$('#origin_password').val();
		 var new_password=$('#new_password').val()
		 var again_password=$('#again_password').val();
		 var error_flag=false;
		 if(origin_password.length<=0)
		 {
			 $('.origin_password').css('display','block');
			 error_flag=true;
		 }
		 else
		 {
			 $('.origin_password').css('display','none');
		 }
		 if(new_password.length<=0)
		 {
			 $('.new_password').css('display','block');
			 error_flag=true;
		 }
		 else
		 {
			 $('.new_password').css('display','none');
		 }
		 if(again_password!=new_password)
		 {
			 $('.again_password').css('display','block');
			 error_flag=true;
		 }
		 else
		 {
			 $('.again_password').css('display','none');
		 }
		 if(error_flag)//界面有错误即退出
			 return;
		 else
		 {
			 $.post("/index.php/index/interfaces/change_password/",
				{
							origin_password:origin_password,
							new_password:new_password,
				}
			 ,
			 function(result){
				if(result=="00")
				{
					$('.origin_password_error').css('display','block');
				}
				else if(result=="01")
				{
					alert(change_password_tips);
					window.location.reload();
				}
			});
		 }
	 });
});

function clock(sec)
{
	if(sec<=0)
	{
		$('#btn_send_email').html(btn_tips);
		$("#btn_send_email").attr("disabled", false);
	}
	else
	{
		$('#btn_send_email').html(sec-- + 's');
		setTimeout(function(){
			clock(sec);
		},1000);
	}
		
}

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


function check_photo()
{
	var filePath ="";
	var fileType ="";
	var fileName = $('#id_photo').val().split('\\'); //得到文件名数组
    var fileSize =  document.getElementById('id_photo').files[0]; //获得文件大小；
    fileName2 = fileName[fileName.length-1]; // 获得文件名
    filePath = $('#id_photo').val().toLowerCase().split(".");
    fileType =  filePath[filePath.length - 1]; //获得文件结尾的类型如 zip rar
    //$('.errHint').show().text(fileName[2]);

	if(fileName.length==1)
	{
		$(".id_photo").css("display","block");
		return false;
	}
    if(!(fileType == "png" || fileType == "jpg")){
        $(".id_photo").css("display","block");
		return false;
    }else if(fileSize.size>2097152){
        $(".id_photo").css("display","block");
        return false;
    }
	$(".id_photo").css("display","none");
	return true;
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
	
	if(check_charnum_profile()&&check_charnum_work_history()&&check_photo()&&check_name()&&check_phone)
	{
		return true;
	}
	else
	{
		return false;
	}
}
