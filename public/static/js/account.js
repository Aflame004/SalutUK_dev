$(document).ready(function () {
	$("#btn_search").click(function(){
		window.location.href='/index.php/index/admin/account/user_name/'+$("#search_user_name").val();
	});
	$(".account_edit").click(function(){
		window.location.href='/index.php/index/admin/account_info_edit/user_id/'+$(this).val();
	});
	
	
   
        //弹出冻结
        $(".account_freeze").click(function(){
                var user_id = $(this).val();
                $('#delcfmModel').val(user_id);
                $('#delcfmModel').modal();  
        
        });
		//弹出启用
		$(".account_enable").click(function(){
                var user_id = $(this).val();
                $('#enablefmModel').val(user_id);
                $('#enablefmModel').modal();  
        
        });

       
});

//确认冻结
function urlSubmit(){
        var user_id=$('#delcfmModel').val()
        $.get("/index.php/index/interfacesadmin/freeze_account/user_id/"+user_id,function(data){
                if(data=="01"){
                        document.location.reload()		
                }
        });
}
//确认启用
function enableSubmit(){
        var user_id=$('#enablefmModel').val()
        $.get("/index.php/index/interfacesadmin/enable_account/user_id/"+user_id,function(data){
                if(data=="01"){
                        document.location.reload()		
                }
        });
}

