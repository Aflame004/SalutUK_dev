$(document).ready(function () {
	$("button").click(function(){
	  var id=$(this).attr("id");
	  window.location.href = "/index.php/index/admin/person_verify_detail/id/"+id;
	});
});