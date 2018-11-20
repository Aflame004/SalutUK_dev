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
use Other\Lib_curl;

class Interfaces extends Controller
{
	/*
	$type 整数
	1:激活邮件;
	2:找回密码;
	*/
	function send_email()//发送邮件
	{
		$request = Request::instance();
		$user_id=Session::get("user_id");
		$type=$request->param('type');
		if($user_id!=null)
		{
			$result = Db::query('select email,token from user where user_id=?',[$user_id]);
			$email=$result[0]['email'];
			$token=$result[0]['token'];
			$mail = new PHPMailer();// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
			//$mail->SMTPDebug = 1;
			// 使用smtp鉴权方式发送邮件
			$mail->isSMTP();
			// smtp需要鉴权 这个必须是true
			$mail->SMTPAuth = true;
			// 邮箱的服务器地址
			$mail->Host = 'smtp.126.com';
			// 设置使用ssl加密方式登录鉴权
			$mail->SMTPSecure = 'ssl';
			// 设置ssl连接smtp服务器的远程服务器端口号
			$mail->Port = 465;
			// 设置发送的邮件的编码
			$mail->CharSet = 'UTF-8';
			// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
			$mail->FromName = 'Test';
			// smtp登录的账号 邮箱即可
			$mail->Username = 'issacsoap@126.com';
			// smtp登录的密码 使用生成的授权码
			$mail->Password = '12345a';
			// 设置发件人邮箱地址 同登录账号
			$mail->From = 'issacsoap@126.com';
			// 邮件正文是否为html编码 注意此处是一个方法
			$mail->isHTML(true);
			// 设置收件人邮箱地址
			$mail->addAddress($email);
			// 添加多个收件人 则多次调用方法即可
			//$mail->addAddress('87654321@163.com');
			if($type=='1')
			{
				// 添加该邮件的主题
				$mail->Subject = 'Test';
				// 添加邮件正文
				$validate_link=Hr_const::domain.Hr_const::validate_email.'/id/'.$user_id.'/token/'.$token;
				$mail->Body = Lang::get('send_email_content').'<a href="'.$validate_link.'">'.$validate_link.'</a>';
			}
			else if($type=='2')
			{
			}
			// 为该邮件添加附件
			//$mail->addAttachment('./example.pdf');
			// 发送邮件 返回状态
			$status = $mail->send();
			//return $this->fetch();
			return Hr_const::success;
		}
	}
	function login()//普通emaillogin
	{
		$request = Request::instance();
		$email=$request->param('email');
		$password=$request->param('password');
		$password = md5($password.Hr_const::salt);
		//排除管理员
		$result = Db::query('select user_id,email,is_activated from user where email=? and password=? and role <>3',[$email,$password]);
		if(!empty($result))
		{
			$user_id=$result[0]['user_id'];
			$status=$result[0]['is_activated'];
			if($status==2)
				return Hr_const::freeze;
			Session::set('user_id',$user_id);
			$login_time=date('Y-m-d H:i:s',time());
			//更新最后登录时间
			Db::execute('update user set last_login_time=? where user_id=?',[$login_time,$user_id]);
			return Hr_const::success;
		}
		else
			return Hr_const::fail;
	}
	function update_user_info()
	{
		$request = Request::instance();
		$user_id=Session::get('user_id');
		$file = $request->file('id_photo');
		$name = $request->param('name');
		$phone = $request->param('phone');
		$age = $request->param('age');
		$profile = $request->param('profile');
		$work_history = $request->param('work_history');
		$info=null;
		// 移动到框架应用根目录/public/uploads/ 目录下
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($info){
			// 成功上传后 获取上传信息
			// 输出 jpg
			//echo $info->getExtension();
			// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
			echo $info->getSaveName();
			// 输出 42a79759f284b767dfcb2a0197904287.jpg
			//echo $info->getFilename();
			}else{
			// 上传失败获取错误信息
				return $file->getError();
			}
		}
		try{
			Db::execute("insert into user_info values(?,?,?,?,?,?,?,?,?)",[$user_id,$name,$info->getSaveName(),$phone,$age,$profile,$work_history,0,'']);
		}catch(PDOException $e){
			Db::execute("update user_info set user_name=?,id_photo=?,phone=?,age=?,profile_info=?,work_history=?,verification_status=? where user_id=?",[$name,$info->getSaveName(),$phone,$age,$profile,$work_history,0,$user_id]);
		}
		$this->redirect('/index.php/index/index');
	}
	public function wechat_login()//微信登录
	{
		$request = Request::instance();
		$code=$request->param("code");
		
		$token_url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.Hr_const::wechat_appid.'&secret='.Hr_const::wechat_secretid.'&code='.$code.'&grant_type=authorization_code';
		$cu=new Lib_curl();
		$token_data=$cu->http($token_url,'get');
		$result=json_decode($token_data[1], TRUE);
		
		$info_url='https://api.weixin.qq.com/sns/userinfo?access_token='.$result['access_token'].'&openid='.$result['openid'].'&lang=zh_CN';
		$info_data=$cu->http($info_url,'get');
		$info_result=json_decode($info_data[1], TRUE);
		
		$unionId=$info_result['unionid'];
		$openId=$info_result['openid'];
		$wechat_name=$info_result['nickname'];
		$result = Db::query('select user_id from user where wechat_union_id like ? and role <>3',[$unionId]);

		if(!empty($result))
		{
			$user_id=$result[0]['user_id'];
			Session::set('user_id',$user_id);
			$login_time=date('Y-m-d H:i:s',time());
			//更新最后登录时间
			Db::execute('update user set last_login_time=?,wechat_name=? where user_id=?',[$login_time,$wechat_name,$user_id]);
			$this->redirect('index.php/index/index/index');
		}
		else
		{
			try{
				$regtime = date('Y-m-d H:i:s',time());
				Db::execute('insert into user(wechat_open_id,wechat_union_id,regist_time,last_login_time,wechat_name,is_activated) 
				values(?,?,?,?,?,?)',[$openId,$unionId,$regtime,$regtime,$wechat_name,1]);
				$result = Db::query('select user_id from user where wechat_union_id like ? and role <>3',[$unionId]);
				$user_id=$result[0]['user_id'];
				Session::set('user_id',$user_id);
			}catch(PDOException $e){
			}
			$this->redirect('index.php/index/index/index');
		}
	}
	public function facebook_login()
	{
		$request = Request::instance();
		$code=$request->param("code");
		if(!empty($request->param("client_secret")))//防止递归获取
			return;
		$cu=new Lib_curl();
		$token_url='https://graph.facebook.com/oauth/access_token?client_id='.Hr_const::fb_appid.'&redirect_uri=https://jobdemo.sprmint.cn/index.php/index/interfaces/facebook_login&client_secret='.Hr_const::fb_secretid.'&code='.$code;
		$token_data=$cu->http($token_url,'get');
		$result=json_decode($token_data[1], TRUE);
		$access_token=$result['access_token'];
		
		$info_url='https://graph.facebook.com/me?access_token='.$access_token;
		$info_data=$cu->http($info_url,'get');
		$info_result=json_decode($info_data[1], TRUE);
		
		$fb_id=$info_result['id'];
		$fb_name=$info_result['name'];
		$result = Db::query('select user_id from user where fb_id like ? and role <>3',[$fb_id]);
		
		if(!empty($result))
		{
			$user_id=$result[0]['user_id'];
			Session::set('user_id',$user_id);
			$login_time=date('Y-m-d H:i:s',time());
			//更新最后登录时间
			Db::execute('update user set last_login_time=?,fb_name=? where user_id=?',[$login_time,$fb_name,$user_id]);
			$this->redirect('index.php/index/index/index');
		}
		else
		{
			try{
				$regtime = date('Y-m-d H:i:s',time());
				Db::execute('insert into user(fb_id,fb_name,regist_time,last_login_time,is_activated) 
				values(?,?,?,?,?)',[$fb_id,$fb_name,$regtime,$regtime,1]);
				$result = Db::query('select user_id from user where fb_id like ? and role <>3',[$fb_id]);
				$user_id=$result[0]['user_id'];
				Session::set('user_id',$user_id);
			}catch(PDOException $e){
			}
			$this->redirect('index.php/index/index/index');
		}
		$this->redirect('index.php/index/index/index');
	}
	function add_company()//个人中心 添加公司
	{
		$request = Request::instance();
		$user_id=Session::get("user_id");
		$company_name=$request->param("company_name");
		$business=$request->param("business");
		$time = date('Y-m-d H:i:s',time());
		$introduction=$request->param("introduction");
		$website=$request->param("website");
		
		Db::execute("insert into company_info(user_id,company_name,business,introduction,website,apply_time,status) values(?,?,?,?,?,?,?)",[$user_id,$company_name,$business,$introduction,$website,$time,0]);
		return Hr_const::success;
		
		//DB::execute('insert into company_info(user_id,name,mobile) values(?,?,?)',[$user_id,$user_name,$mobile]);
	}
	function del_company()
	{
		$request = Request::instance();
		$company_id=$request->param("company_id");
		$user_id=Session::get('user_id');
		Db::execute('delete from company_info where user_id=? and company_id=?',[$user_id,$company_id]);
		return Hr_const::success;
	}
	function change_password()
	{
		$request = Request::instance();
		$user_id=Session::get('user_id');
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

	function change_by_token(){
		$request = Request::instance();
		$token=$request->param('token');
		$password=$request->param('password');
		$result=Db::query('select user_id from user where token=?',[$token]);
		if(!empty($result)){
			$user_id=$result[0]['user_id'];
			$new_password=md5($password.Hr_const::salt);
			Db::execute('update user set password=? where user_id=?',[$new_password,$user_id]);
			return Hr_const::success;
		}else{
			return Hr_const::fail;

		}

	}

	function forget_pwd(){
		$request = Request::instance();
		$email=input('email');
		$result = Db::query('select user_id,email,password from user where email = ?',[$email]);
		if(empty($result)){
			return Hr_const::fail;
		}
		$email=$result[0]['email'];
		$password=$result[0]['password'];
		$user_id=$result[0]['user_id'];
		$now=time();
		$token = md5($email.$password.$now);
		if(!empty($email)){
			$mail = new PHPMailer();// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
			//$mail->SMTPDebug = 1;
			// 使用smtp鉴权方式发送邮件
			$mail->isSMTP();
			// smtp需要鉴权 这个必须是true
			$mail->SMTPAuth = true;
			// 邮箱的服务器地址
			$mail->Host = 'smtp.126.com';
			// 设置使用ssl加密方式登录鉴权
			$mail->SMTPSecure = 'ssl';
			// 设置ssl连接smtp服务器的远程服务器端口号
			$mail->Port = 465;
			// 设置发送的邮件的编码
			$mail->CharSet = 'UTF-8';
			// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
			$mail->FromName = 'Test';
			// smtp登录的账号 邮箱即可
			$mail->Username = 'issacsoap@126.com';
			// smtp登录的密码 使用生成的授权码
			$mail->Password = '12345a';
			// 设置发件人邮箱地址 同登录账号
			$mail->From = 'issacsoap@126.com';
			// 邮件正文是否为html编码 注意此处是一个方法
			$mail->isHTML(true);
			// 设置收件人邮箱地址
			$mail->addAddress($email);
			// 添加多个收件人 则多次调用方法即可
			//$mail->addAddress('87654321@163.com');
				// 添加该邮件的主题
				$mail->Subject = 'Test';
				// 添加邮件正文
				$validate_link=Hr_const::domain.Hr_const::change_pwd_email.'?token='.$token;
				$mail->Body = Lang::get('send_email_content_2').'<a href="'.$validate_link.'">'.$validate_link.'</a>';
				Db::execute('update user set token=? where user_id=?',[$token,$user_id]);
			// 为该邮件添加附件
			//$mail->addAttachment('./example.pdf');
			// 发送邮件 返回状态
			$status = $mail->send();
			//return $this->fetch();
			return Hr_const::success;
		}
	}

}