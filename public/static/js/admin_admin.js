$(document).ready(function () {
        //弹出编辑 
	$(".admin_edt").click(function(){
                $("#myModalLabel").text("编辑");
                $('#btn_submit').val($(this).val())
                $('#myModal').modal();
        });
        //重置密码
        $(".admin_edt_pwd").click(function(){
                var user_id = $(this).val();
                $('#resetPwdModel').val(user_id);
                $('#resetPwdModel').modal();  
        });
        //弹出冻结
        $(".admin_cold").click(function(){
                var user_id = $(this).val();
                $('#delcfmModel').val(user_id);
                $('#delcfmModel').modal();  
        
        });
        //修改信息
        $("#btn_submit").click(function(){
                var user_id = $('#btn_submit').val();
                var name = $('#name').val();
                var mobile=$('#mobile').val();
                var group_choose=$('#group_choose option:selected').val();
                $.get("/index.php/index/interfacesadmin/modify_admin/id/"+user_id+'/name/'+name+'/mobile/'+mobile+'/admin_group/'+group_choose,function(data){
                        if(data=="01"){
                                document.location.reload()		
                        }
                });
        });
        //弹出添加管理员
        $(".admin_add").click(function(){
                $("#addModalLabel").text("编辑");
                $('#addModal').modal();
        });
        //添加管理员
        $("#add_btn_submit").click(function(){
                var email=$('#add_admin_email').val();
                var password=$('#add_password').val();
                var name = $('#add_name').val();
                var mobile=$('#add_mobile').val();
                $(".add_admin_email").css('display','none');
                $(".add_password").css('display','none');
                $(".add_name").css('display','none');
                $(".add_admin_group").css('display','none');
                if(email.length==0){
                        $(".add_admin_email").css('display','block');
                        return;
                }
                if(password.length==0){
                        $(".add_password").css('display','block');
                        return;
                }
                if(name.length==0){
                        $(".add_name").css('display','block');
                        return;
                }
                var group_choose=$('#add_group_choose option:selected').val();
                if(group_choose.length==0){
                        $(".add_admin_group").css('display','block');
                        return;
                }
                $.get("/index.php/index/interfacesadmin/add_admin/email/"+email+'/name/'+name+"/password/"+password+'/admin_group/'+group_choose+'/mobile/'+mobile,function(data){
                        if(data=="01"){
                                document.location.reload()		
                        }
                });
        })
       
});

//确认冻结
function urlSubmit(){
        var user_id=$('#delcfmModel').val()
        $.get("/index.php/index/interfacesadmin/cold_admin/id/"+user_id,function(data){
                if(data=="01"){
                        document.location.reload()		
                }
        });
}

function resetSubmit(){
        var user_id=$('#resetPwdModel').val()
        $.get("/index.php/index/interfacesadmin/reset_admin_pwd/id/"+user_id,function(data){
                if(data=="01"){
                        document.location.reload()		
                }
        });
}
