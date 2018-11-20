$(document).ready(function () {
	$(".next_match").click(function(){
	  var id=$(this).val();
	  window.location.href = "/index.php/index/admin/match_next/id/"+id;
	});
});