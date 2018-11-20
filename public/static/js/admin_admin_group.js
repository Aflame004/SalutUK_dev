$(document).ready(function () {
        //弹出编辑 
	$(".group_edit").click(function(){
                $('#edit_btn_submit').val($(this).val())
                $('#editModal').modal();
				$.get('/index.php/index/interfacesadmin/get_auth_info/group_id/'+$(this).val(),function(data){
					var get_arr = data.split(',');
					$("#edit_group_name").val(get_arr[0]);
					get_arr=get_arr.slice(1);
					var check_boxes = $(".edit_auth");
							check_boxes.each(function(){
									$(this).attr("checked", false);
							});
					if(get_arr.length>0)
					{
						for(i=0;i<get_arr.length;i++)
						{
							var check_boxes = $(".edit_auth");
							check_boxes.each(function(){
								if($(this).val()==get_arr[i])
								{
									$(this).attr("checked", true);
								}
									
						   });
						}
							
					}
				});
        });
        //弹出冻结
        $(".group_freeze").click(function(){
                var group_id = $(this).val();
                $('#delcfmModel').val(group_id);
                $('#delcfmModel').modal();  
        
        });
        //修改信息
        $("#edit_btn_submit").click(function(){
				var group_id=$(this).val();
                var admin_group_name=$('#edit_group_name').val();
                var check_boxes = $(".edit_auth");
				var auth_id_array=new Array();
				check_boxes.each(function(){
						if($(this).is(':checked'))
                        auth_id_array.push($(this).val());  
                   });
                $.post("/index.php/index/interfacesadmin/edit_admin_group",
						{
							group_id:group_id,
							admin_group_name:admin_group_name,
							auth_id_array:auth_id_array
						},
						function(data){
							alert(data);
                        if(data=="01"){
                                document.location.reload()		
                        }
                });
        });
        //弹出新增权限组
        $(".admin_add").click(function(){
                $('#addModal').modal();
        });
        //添加权限组
        $("#add_btn_submit").click(function(){
                var admin_group_name=$('#admin_group_name').val();
                var check_boxes = $(".add_auth");
				var auth_id_array=new Array();
				check_boxes.each(function(){
						if($(this).is(':checked'))
                        auth_id_array.push($(this).val());  
                   });
                $.post("/index.php/index/interfacesadmin/add_admin_group",
						{
							admin_group_name:admin_group_name,
							auth_id_array:auth_id_array
						},
						function(data){
                        if(data=="01"){
                                document.location.reload()		
                        }
                });
        })
       
});

//确认冻结
function urlSubmit(){
        var group_id=$('#delcfmModel').val()
        $.get("/index.php/index/interfacesadmin/freeze_admin_grouop/group_id/"+group_id,function(data){
                if(data=="01"){
                        document.location.reload()		
                }
        });
}

