$(document).ready(function () {
	$(".btn_detail").click(function(){
	  var company_id=$(this).val();
	  window.location.href = "/index.php/index/admin/company_verify_detail/company_id/"+company_id;
	});
});