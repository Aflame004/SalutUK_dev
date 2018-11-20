$(document).ready(function () {
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
            $.post("/index.php/index/interfacesadmin/change_password/",
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
