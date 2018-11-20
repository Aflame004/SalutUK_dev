<?php
namespace app\index\controller;

use think\Controller;
use think\View;
use think\Lang;
use think\Request;
use think\Session;
use think\Db;
use think\exception\PDOException;
use PHPMailer\PHPMailer;
use Hr_const\Hr_const;

class Interfacesadmin extends Controller
{
	function login()
	{
		$request = Request::instance();
		$email=$request->param('email');
		$password=$request->param('password');
		$password = md5($password.Hr_const::salt);
		$result = Db::query('select user_id,email,admin_group,is_activated from user where email=? and password=? and role=3',[$email,$password]);
		if(!empty($result))
		{
			Session::set('admin_email',$email);
			$is_activated=$result[0]['is_activated'];
			$login_time=date('Y-m-d H:i:s',time());
			$group_id=$result[0]['admin_group'];
			$user_id=$result[0]['user_id'];
			$result=Db::query('select status from admin_group_describe where group_id=?',[$group_id]);
			if($result[0]['status']==0||$is_activated==2)//0代表组冻结，1代表账户本身冻结
				return Hr_const::freeze;
			Session::set('admin_user_id',$user_id);
			//更新最后登录时间
			Db::execute('update user set last_login_time=? where email=?',[$login_time,$email]);
			return Hr_const::success;
		}
		else
			return Hr_const::fail;
	}
	function person_verify_success()
	{
		if($this->check_auth())
		{
			$request = Request::instance();
			$user_id=$request->param("id");
			DB::execute('update user_info set verification_status=1 where user_id=?',[$user_id]);
			return Hr_const::success;
		}
	}
	function person_verify_fail()
	{
		if($this->check_auth())
		{
			$request = Request::instance();
			$user_id=$request->param("id");
			$fail_reason=$request->param("reason");//审核失败理由
			DB::execute('update user_info set verification_status=2,fail_reason=? where user_id=?',[$fail_reason,$user_id]);
			return Hr_const::success;
		}
	}
	function match()
	{
		if($this->check_auth())
		{
			$request = Request::instance();
			$job1=$request->param("job1");
			$job2=$request->param("job2");
			DB::execute('insert into job_match(job_hunting_id,job_publishment_id,status,match_time) values(?,?,1,?)',[$job2,$job1,date('Y-m-d H:i:s',time())]);
			DB::execute('update job_hunting set status=2 where id=?',[$job2]);
			DB::execute('update job_publishment set status=2 where id=?',[$job1]);
			return Hr_const::success;
		}
	}
	function match_complete()
	{
		$this->check_auth();
		$request = Request::instance();
		$id=$request->param("id");
		DB::execute('update job_match set status=2 where id=?',[$id]);
		return Hr_const::success;
	}
	function match_cancel()
	{
		$this->check_auth();
		$request = Request::instance();
		$id=$request->param("id");
		DB::execute('delete from job_match where id=?',[$id]);
		return Hr_const::success;
	}
	//修改信息
	function modify_admin(){
		$this->check_auth();
		$request = Request::instance();
		$id=$request->param("id");
		$user_name=$request->param("name");
		$mobile=$request->param("mobile");
		$admin_group=$request->param("admin_group");
		DB::execute('update  admin_info set name = ?, mobile = ?  where user_id=?',[$user_name,$mobile,$id]);
		DB::execute('update user set admin_group = ? where user_id = ?',[$admin_group,$id]);
		return Hr_const::success;
	}
	//冻结账号
	function cold_admin(){
		$this->check_auth();
		$request = Request::instance();
		$id=$request->param("id");
		DB::execute('update user set is_activated = 2  where user_id =?',[$id]);
		return Hr_const::success;
	}
	//重置管理员密码为123456
	function reset_admin_pwd(){
		$this->check_auth();
		$request = Request::instance();
		$id=$request->param("id");
		$pwd=md5('123456'.Hr_const::salt);
		DB::execute('update user set password = ?  where user_id =?',[$pwd,$id]);
		return Hr_const::success;
	}
	//修改密码
	function change_password()
	{
		$request = Request::instance();
		$user_id=Session::get('admin_user_id');
		$origin_password=$request->param('origin_password');
		$new_password=$request->param('new_password');
		
		$result=Db::query('select user_id,password from user where user_id=?',[$user_id]);
		$origin_password=md5($origin_password.Hr_const::salt);
		$new_password=md5($new_password.Hr_const::salt);
		if(!strcmp($result[0]['password'],$origin_password))
		{
			Db::execute('update user set password=? where user_id=?',[$new_password,$user_id]);
			return Hr_const::success;
		}
		else
		{
			return Hr_const::fail;
		}
	}
	//新增用户
	function add_admin(){
		$this->check_auth();
		$request = Request::instance();
		$email=$request->param("email");
		$user_name=$request->param("name");
		$regtime = date('Y-m-d H:i:s',time());
		$mobile=$request->param("mobile");
		$admin_group=$request->param("admin_group");
		$password=$request->param("password");
		DB::execute('insert into user(email,password,is_activated,admin_group,regist_time,role) values(?,?,1,?,?,3)',[$email,md5($password.Hr_const::salt),$admin_group,$regtime]);
		$result = Db::query('select user_id from user where email  = ?',[$email]);
		$user_id=$result[0]['user_id'];
		DB::execute('insert into admin_info(user_id,name,mobile) values(?,?,?)',[$user_id,$user_name,$mobile]);
		return Hr_const::success;
	}

	function company_verify_success()
	{
		$this->check_auth();
		$request = Request::instance();
		$company_id=$request->param('company_id');
		Db::execute('update company_info set status=1 where company_id=?',[$company_id]);
		$result=Db::query('select user_id from company_info where company_id=?',[$company_id]);
		Db::execute('update user set role=2 where user_id=?',[$result[0]['user_id']]);
		return Hr_const::success;
	}
	function company_verify_fail()
	{
		$this->check_auth();
		$request = Request::instance();
		$company_id=$request->param("company_id");
		$fail_reason=$request->param("reason");//审核失败理由
		DB::execute('update company_info set status=2,fail_reason=? where company_id=?',[$fail_reason,$company_id]);
		return Hr_const::success;
	}
	function add_admin_group()
	{
		$this->check_auth();
		$request = Request::instance();
		$admin_group_name=$request->param('admin_group_name');
		$auth_id_array = $_POST['auth_id_array'];
		
		Db::execute('insert into admin_group_describe(group_name) values(?)',[$admin_group_name]);
		$group_id=Db::getLastInsID();
		foreach($auth_id_array as $item)
		{
			Db::execute('insert into admin_group values(?,?)',[$group_id,$item]);
		}
		return Hr_const::success;
	}
	function get_auth_info()//编辑权限组时，返回权限名和具体权限
	{
		$this->check_auth();
		$request = Request::instance();
		$group_id=$request->param('group_id');
		$temp2=Db::query('select * from admin_group_describe where group_id=?',[$group_id]);
		$return_str=$temp2[0]['group_name'];
		$temp=Db::query('select * from admin_group where group_id=?',[$group_id]);
		foreach($temp as $item)
		{
			$return_str=$return_str.','.$item['auth_id'];
		}
		return $return_str;
	}
	function edit_admin_group()
	{
		$this->check_auth();
		$request = Request::instance();
		$group_id=$request->param('group_id');
		$admin_group_name=$request->param('admin_group_name');
		$auth_id_array = $_POST['auth_id_array'];
		
		Db::execute('update admin_group_describe set group_name=? where group_id=?',[$admin_group_name,$group_id]);
		Db::execute('delete from admin_group where group_id=?',[$group_id]);
		foreach($auth_id_array as $item)
		{
			Db::execute('insert into admin_group values(?,?)',[$group_id,$item]);
		}
		return Hr_const::success;
	}
	function freeze_admin_grouop(){
		$this->check_auth();
		$request = Request::instance();
		$group_id=$request->param('group_id');
		DB::execute('update admin_group_describe set status = 0  where group_id =?',[$group_id]);
		return Hr_const::success;
	}
	function update_user_info()
	{
		$request = Request::instance();
		$user_id=$request->param('user_id');
		$name = $request->param('name');
		$phone = $request->param('phone');
		$age = $request->param('age');
		$profile = $request->param('profile');
		$work_history = $request->param('work_history');
		$role = $request->param('role');

		Db::execute('update user_info set user_name=?,phone=?,age=?,profile_info=?,work_history=?,verification_status=? where user_id=?',[$name,$phone,$age,$profile,$work_history,1,$user_id]);
		Db::execute('update user set role=? where user_id=?',[$role,$user_id]);
		$this->redirect('/index.php/index/admin/account');
	}
	//冻结普通账号
	function freeze_account(){
		$this->check_auth();
		$request = Request::instance();
		$user_id=$request->param('user_id');
		DB::execute('update user set is_activated = 2 where user_id =?',[$user_id]);
		return Hr_const::success;
	}
	//启用普通账号
	function enable_account(){
		$this->check_auth();
		$request = Request::instance();
		$user_id=$request->param('user_id');
		DB::execute('update user set is_activated = 1 where user_id =?',[$user_id]);
		return Hr_const::success;
	}
	private function check_auth()
	{
		if(Session::has('admin_email'))
		{
			return true;
		}
		else
		{
			$this->redirect("/index.php/index/admin/login");
			return false;
		}	
	}
}