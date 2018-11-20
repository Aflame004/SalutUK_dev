$(document).ready(function () {

});

function check_first_name()
{
	if($("#first_name").val().length<=0)
	{
		$(".first_name").css('display','block');
		return false;
	}
	else
	{
		$(".first_name").css('display','none');
		return true;
	}
}

function check_surname()
{
	if($("#surname").val().length<=0)
	{
		$(".surname").css('display','block');
		return false;
	}
	else
	{
		$(".surname").css('display','none');
		return true;
	}
}
function check_street()
{
	if($("#street").val().length<=0)
	{
		$(".street").css('display','block');
		return false;
	}
	else
	{
		$(".street").css('display','none');
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
function check_state()
{
	if($("#state").val().length<=0)
	{
		$(".state").css('display','block');
		return false;
	}
	else
	{
		$(".state").css('display','none');
		return true;
	}
}

function check_postal()
{
	if($("#postal").val().length<=0)
	{
		$(".postal").css('display','block');
		return false;
	}
	else
	{
		$(".postal").css('display','none');
		return true;
	}
}

function check_mobile_district()
{
	if($("#mobile_district").val().length<=0)
	{
		$(".mobile_district").css('display','block');
		return false;
	}
	else
	{
		$(".mobile_district").css('display','none');
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
function check_emergency_first_name()
{
	if($("#emergency_first_name").val().length<=0)
	{
		$(".emergency_first_name").css('display','block');
		return false;
	}
	else
	{
		$(".emergency_first_name").css('display','none');
		return true;
	}
}

function check_emergency_last_name()
{
	if($("#emergency_last_name").val().length<=0)
	{
		$(".emergency_last_name").css('display','block');
		return false;
	}
	else
	{
		$(".emergency_last_name").css('display','none');
		return true;
	}
}

function check_emergency_mobile()
{
	if($("#emergency_mobile").val().length<=0)
	{
		$(".emergency_mobile").css('display','block');
		return false;
	}
	else
	{
		$(".emergency_mobile").css('display','none');
		return true;
	}
}
function check_emergency_relationship()
{
	if($("#emergency_relationship").val().length<=0)
	{
		$(".emergency_relationship").css('display','block');
		return false;
	}
	else
	{
		$(".emergency_relationship").css('display','none');
		return true;
	}
}
function check_work_skills()
{
	if($("#work_skills").val().length<=0)
	{
		$(".work_skills").css('display','block');
		return false;
	}
	else
	{
		$(".work_skills").css('display','none');
		return true;
	}
}
function check_work_start_date(){
	var data=$('#work_start_date').val();
	if(isNaN(data)&&!isNaN(Date.parse(data))){
		$(".work_start_date").css('display','none');
		return true;
	}else{
		$(".work_start_date").css('display','block');
		return false;
	}
}
function check_birthday(){
	var data = $('#birthday').val();
	if(isNaN(data)&&!isNaN(Date.parse(data))){
		$(".birthday").css('display','none');
		return true;
	}else{
		$(".birthday").css('display','block');
		return false;
	}
}
function check_spcify_job(){
	var number=$('input:checkbox[name="specify_job[]"]:checked').length;
	if($("#specify_job2").val().length>0){
		number++;
	}
	if(number>0){
		$(".specify_job").css('display','none');
		$(".specify_job2").css('display','none');
		return true;
	}else{
		$(".specify_job").css('display','block');
		$(".specify_job2").css('display','block');
		return false;
	}

}
function check_multi_language(){
	var number=$('input:checkbox[name="multi_language[]"]:checked').length;
	if(number>0){
		$(".multi_language").css('display','none');
		return true;
	}else{
		$(".multi_language").css('display','block');
		return false;
	}

}
function check_id_photo()
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

function check_cv()
{
	var filePath ="";
	var fileType ="";
	var fileName = $('#upload_cv').val().split('\\'); //得到文件名数组
    var fileSize =  document.getElementById('upload_cv').files[0]; //获得文件大小；
    fileName2 = fileName[fileName.length-1]; // 获得文件名
    filePath = $('#upload_cv').val().toLowerCase().split(".");
    fileType =  filePath[filePath.length - 1]; //获得文件结尾的类型如 zip rar
    //$('.errHint').show().text(fileName[2]);
	if(fileName.length==1)
	{
		$(".upload_cv").css("display","none");
		return true;
	}
    if(!(fileType == "doc" || fileType == "docx" || fileType == "pdf")){
        $(".upload_cv").css("display","block");
		return false;
    }else if(fileSize.size>2097152){
        $(".upload_cv").css("display","block");
        return false;
    }
	$(".upload_cv").css("display","none");
	return true;
}

function submit_check()
{
	if(check_multi_language()&&check_spcify_job()&&check_work_start_date()&&check_birthday()&&check_first_name()&&check_surname()&&check_street()&&check_city()&&check_state()&&check_postal()&&check_mobile_district()&&check_mobile()&&check_emergency_first_name()&&check_emergency_last_name()&&check_emergency_mobile()&&check_emergency_relationship()&&check_work_skills()&&check_id_photo()&&check_cv())
	{
		return true;
	}
	else
	{
		return false;
	}
}