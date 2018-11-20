$(document).ready(function () {

});

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

function check_business_name()
{
	if($("#business_name").val().length<=0)
	{
		$(".business_name").css('display','block');
		return false;
	}
	else
	{
		$(".business_name").css('display','none');
		return true;
	}
}

function check_address()
{
	if($("#address").val().length<=0)
	{
		$(".address").css('display','block');
		return false;
	}
	else
	{
		$(".address").css('display','none');
		return true;
	}
}

function check_city()
{
	if($("#city").val().length<=0)
	{
		$(".city").css('display','block');
		return false;
	}
	else
	{
		$(".city").css('display','none');
		return true;
	}
}

function check_country()
{
	if($("#country").val().length<=0)
	{
		$(".country").css('display','block');
		return false;
	}
	else
	{
		$(".country").css('display','none');
		return true;
	}
}

function check_postcode()
{
	if($("#postcode").val().length<=0)
	{
		$(".postcode").css('display','block');
		return false;
	}
	else
	{
		$(".postcode").css('display','none');
		return true;
	}
}

function check_landline()
{
	if($("#landline").val().length<=0)
	{
		$(".landline").css('display','block');
		return false;
	}
	else
	{
		$(".landline").css('display','none');
		return true;
	}
}

function check_company_name()
{
	if($("#company_name").val().length<=0)
	{
		$(".company_name").css('display','block');
		return false;
	}
	else
	{
		$(".company_name").css('display','none');
		return true;
	}
}

function check_job_vacancy()
{
	if($("#job_vacancy").val().length<=0)
	{
		$(".job_vacancy").css('display','block');
		return false;
	}
	else
	{
		$(".job_vacancy").css('display','none');
		return true;
	}
}

function check_contact_person()
{
	if($("#contact_person").val().length<=0)
	{
		$(".contact_person").css('display','block');
		return false;
	}
	else
	{
		$(".contact_person").css('display','none');
		return true;
	}
}
function check_mobile()
{
	if($("#mobile").val().length<=0)
	{
		$(".mobile").css('display','block');
		return false;
	}
	else
	{
		$(".mobile").css('display','none');
		return true;
	}
}

function check_wechat()
{
	if($("#wechat").val().length<=0)
	{
		$(".wechat").css('display','block');
		return false;
	}
	else
	{
		$(".wechat").css('display','none');
		return true;
	}
}
function check_start_date(){
	var data = $('#start_date').val();
	if(isNaN(data)&&!isNaN(Date.parse(data))){
		$(".start_date").css('display','none');
		return true;
	}else{
		$(".start_date").css('display','block');
		return false;
	}
}

function check_address_upload()
{
	var filePath ="";
	var fileType ="";
	var fileName = $('#address_upload').val().split('\\'); //得到文件名数组
    var fileSize =  document.getElementById('address_upload').files[0]; //获得文件大小；
    fileName2 = fileName[fileName.length-1]; // 获得文件名
    filePath = $('#address_upload').val().toLowerCase().split(".");
    fileType =  filePath[filePath.length - 1]; //获得文件结尾的类型如 zip rar
    //$('.errHint').show().text(fileName[2]);
	if(fileName.length==1)
	{
		$(".address_upload").css("display","none");
		return true;
	}
    if(!(fileType == "jpeg" || fileType == "jpg" || fileType == "pdf")){
        $(".address_upload").css("display","block");
		return false;
    }else if(fileSize.size>2097152){
        $(".address_upload").css("display","block");
        return false;
    }
	$(".upload_cv").css("display","none");
	return true;
}

function submit_check()
{
	if(check_start_date()&&check_name()&&check_business_name()&&check_address()&&check_city()&&check_city()&&check_country()&&check_postcode()&&check_landline()&&check_mobile()&&check_wechat())
	{
		return true;
	}
	else
	{
		return false;
	}
}