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

class Index extends Controller
{
	public function myinfo()
	{
		return phpinfo();
	}
    public function index()
    {
		return $this->fetch();
	}
	public function login()
	{
		return $this->fetch();
	}
	public function logout()
	{
		Session::clear();
		$this->redirect('/index.php/index/index/index');
	}
	public function register()
	{
		$request = Request::instance();
		$email_exist=0;
		if(Session::get('user_id')!=null)
			$this->redirect('/index.php/index/index/index');
		else if($request->has('email','post')&&$request->has('password','post'))
		{
			$email = $request->post('email');
			$password = $request->post('password');
			$password = md5($password.Hr_const::salt);
			$regtime = date('Y-m-d H:i:s',time());
			$token = md5($email.$password.$regtime);
			$result=Db::execute('select user_id from user where email=?',[$email]);
			if(!empty($result))
			{
				$email_exist=1;
			}
			else
			{
				try{
					$count=Db::execute('insert into user(email,password,regist_time,last_login_time,token) 
					values(?,?,?,?,?)',[$email,$password,$regtime,$regtime,$token]);
					$result=DB::query('select user_id from user where email=?',[$email]);
					$user_id=$result[0]['user_id'];
					Session::set('user_id',$user_id);
					$this->redirect('index.php/index/index/send_email');
				}catch(PDOException $e){
				}
			}
		}
		$this->assign('email_exist',$email_exist);
		return $this->fetch();
	}
	public function send_email()
	{
		$user_id=Session::get('user_id');
		$result=DB::query('select email from user where user_id=?',[$user_id]);
		$email=$result[0]['email'];
		$this->assign('email',$email);
		return $this->fetch();
	}
	function validate_email()//确认邮件
	{
		$request = Request::instance();
		$id=$request->param('id');
		$id=(int)$id;
		$token=$request->param('token');
		$count=0;
		try{
			$count=DB::execute('update user set is_activated=1 where user_id=? and token=?',[$id,$token]);
		}catch(PDOException $e){
			$count=0;
		}
		if($count==0){
			$this->assign('check_result',Hr_const::fail);
		}
		else
		{
			$this->assign('check_result',Hr_const::success);
		}
		return $this->fetch();
	}
	function job_hunting()
	{
		if(Session::has('user_id'))//检查是否登录
		{
			$user_id=Session::get('user_id');
			$result=DB::query('select is_activated from user where user_id=?',[$user_id]);
			$is_activated=$result[0]['is_activated'];
			$this->assign('is_activated',$is_activated);

			if($is_activated==1)
			{
				$result=DB::query('select * from user_info where user_id=?',[$user_id]);
				if(sizeof($result)==0)//如果没有找到信息，那么先完善个人信息
				{
					$this->redirect("/index.php/index/index/mmcenter/choose/2");
				}
				else
				{
					return $this->fetch();
				}
			}
			
			return $this->fetch();
		}
		else
		{
			$this->redirect("/index.php/index/index/login");
		}
	}
	function mmcenter()//个人中心
	{
		$request = Request::instance();
		if(Session::has('user_id'))
		{
			$user_id=Session::get('user_id');
			$result=Db::query("select is_activated from user where user_id=?",[$user_id]);
			$is_activated=$result[0]['is_activated'];
			$result=Db::query("select * from user_info where user_id=?",[$user_id]);
			$verify=null;
			$reason=null;
			if(empty($result))
				$verify=-1;//没有提交账户审核信息
			else
			{
				$verify=$result[0]['verification_status'];
				$reason=$result[0]['fail_reason'];
			}
				
			$choose=1;
			if($request->has('choose'))//1代表进入当前申请2代表进入账户资料3代表进入公司管理
			{
				$choose=$request->param('choose');
			}
			if($choose==1)
			{
				$result_1=Db::query('select * from job_hunting where user_id=? order by apply_time',[$user_id]);
				$this->assign('current_apply',$result_1);
			}
			$this->assign('choose',$choose);
			$this->assign('verify',$verify);
			$this->assign('reason',$reason);
			$this->assign('is_activated',$is_activated);
			if($choose==3)
			{
				$result_3=Db::query('select * from company_info where user_id=?',[$user_id]);
				$this->assign('company_info',$result_3);
			}
			if($choose==2)
			{
				$result_2=Db::query('select * from user_info where user_id=?',[$user_id]);
				if(!empty($result_2))
					$this->assign('result_2',$result_2[0]);
				else
					$this->assign('result_2',null);
			}
			return $this->fetch();
		}
		else
		{
			$this->redirect("/index.php/index/index/login");
		}
	}
	function job_publishment()
	{
		if(Session::has('user_id'))//检查是否登录
		{
			$user_id=Session::get('user_id');
			$result=DB::query('select is_activated from user where user_id=?',[$user_id]);
			$is_activated=$result[0]['is_activated'];
			$this->assign('is_activated',$is_activated);

			if($is_activated==1)
			{
				$result=DB::query('select * from user_info where user_id=?',[$user_id]);
				if(sizeof($result)==0)//如果没有找到信息，那么先完善个人信息
				{
					$this->redirect("/index.php/index/index/mmcenter/choose/2");
				}
				else
				{
					return $this->fetch();
				}
			}
			
			return $this->fetch();
		}
		else
		{
			$this->redirect("/index.php/index/index/login");
		}
	}
	
	
	
	function submit_job_hunting()
	{
		$insert_result='00';
		if(Session::has('user_id'))
		{
			$user_id=Session::get('user_id');
			$request = Request::instance();
			//$specify = $request->param('specify_job');
			$first_name=$request->param('first_name');
			$surname=$request->param('surname');
			$specify_arr=null;
			if(isset($_POST['specify_job']))
				$specify_arr = $_POST['specify_job'];
			$specify='';
			for($i=0;$i<sizeof($specify_arr);$i++)
			{
				$specify=$specify.$specify_arr[$i].',';
			}
			$specify_job2=$request->param('specify_job2');
			$work_start_date=$request->param('work_start_date');
			$city_want=$request->param('city_want');
			$right_uk=$request->param('right_uk');
			$hunting_gender=$request->param('hunting_gender');
			$height=$request->param('height');
			$weight=$request->param('weight');
			$birthday=$request->param('birthday');
			$marital_status=$request->param('marital_status');
			$street=$request->param('street');
			$street2=$request->param('street2');
			$city=$request->param('city');
			$state=$request->param('state');
			$postal=$request->param('postal');
			$mobile_district=$request->param('mobile_district');
			$mobile=$request->param('mobile');
			$email=$request->param('email');
			$insurance_num=$request->param('insurance_num');
			$emergency_first_name=$request->param('emergency_first_name');
			$emergency_last_name=$request->param('emergency_last_name');
			$emergency_mobile=$request->param('emergency_mobile');
			$emergency_relationship=$request->param('emergency_relationship');
			$education=$request->param('education');
			$language_arr=null;
			if(isset($_POST['multi_language']))
				$language_arr = $_POST['multi_language'];
			$language='';
			for($i=0;$i<sizeof($language_arr);$i++)
			{
				$language=$language.$language_arr[$i].',';
			}
			$fluency_english=$request->param('fluency_english');
			$driver_licences=$request->param('driver_licences');
			$employment_history=$request->param('employment_history');
			$work_skills=$request->param('work_skills');
			$salary=$request->param('salary');
			$criminal=$request->param('criminal');
			$criminal_detail=$request->param('criminal_detail');
			$health=$request->param('health');
			$health_detail=$request->param('health_detail');
			$where_know=$request->param('where_know');
			$upload_cv = $request->file('upload_cv');
			$id_photo = $request->file('id_photo');
			$cv_path="";
			$id_photo_path="";
			
			if($upload_cv){
				$info = $upload_cv->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info)
				{
					$cv_path=$info->getSaveName();
				}
			}
			if($id_photo){
				$info = $id_photo->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info)
				{
					$id_photo_path=$info->getSaveName();
				}
			}
			
				$result=Db::execute("
					insert into job_hunting(user_id,first_name,surname,specify,specify2,work_start_date,city_want,right_uk,gender,height,weight,birthday,marital_status,street,street2,city,`state`,postal,mobile_district,mobile,email,insurance_num,emergency_first_name,emergency_last_name,emergency_mobile,emergency_relationship,education,`language`,fluency_english,driver_licences,employment_history,work_skills,salary,criminal,criminal_detail,health,health_detail,where_know,upload_cv,id_photo,apply_time,status) values(
						?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,
						?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
					)
				",[
					$user_id,
					$first_name,
					$surname,
					$specify,
					$specify_job2,
					$work_start_date,
					$city_want,
					$right_uk,
					$hunting_gender,
					$height,
					$weight,
					$birthday,
					$marital_status,
					$street,
					$street2,
					$city,
					$state,
					$postal,
					$mobile_district,
					$mobile,
					$email,
					$insurance_num,
					$emergency_first_name,
					$emergency_last_name,
					$emergency_mobile,
					$emergency_relationship,
					$education,
					$language,
					$fluency_english,
					$driver_licences,
					$employment_history,
					$work_skills,
					$salary,
					$criminal,
					$criminal_detail,
					$health,
					$health_detail,
					$where_know,
					$cv_path,
					$id_photo_path,
					date('Y-m-d H:i:s',time()),
					1
				]
				);
				$insert_result='01';
			
		}
		else
		{
			$this->redirect('index.php/index/index/login');
		}
		$this->assign('insert_result',$insert_result);
		return $this->fetch();
	}
	function submit_job_publishment()
	{
		$insert_result='00';
		if(Session::has('user_id'))
		{
			$user_id=Session::get('user_id');
			$request = Request::instance();
			$name=$request->param('name');
			$business_name=$request->param('business_name');

			$address=$request->param('address');
			$city=$request->param('city');
			$country=$request->param('country');
			$postcode=$request->param('postcode');
			$landline=$request->param('landline');
			$accomodation=$request->param('accomodation');
			$distance=$request->param('distance');
			$company_name=$request->param('company_name');
			$vat_number=$request->param('vat_number');
			$job_vacancy=$request->param('job_vacancy');
			$job_Specification1=$request->param('job_Specification1');
			$job_Specification2=$request->param('job_Specification2');
			$job_Specification3=$request->param('job_Specification3');
			$skills_required=$request->param('skills_required');
			$start_date=$request->param('start_date');
			$special=$request->param('special');
			$contact_person=$request->param('contact_person');
			$mobile=$request->param('mobile');
			$wechat=$request->param('wechat');
			$email=$request->param('email');
			$where_know=$request->param('where_know');
			
			$address_upload = $request->file('address_upload');
			$au_path="";

			if($address_upload){
				$info = $address_upload->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info)
				{
					$au_path=$info->getSaveName();
				}
			}

			
				$result=Db::execute("
					insert into job_publishment(user_id,name,business_name,address,city,country,postcode,landline,accomodation,distance,company_name,vat_num,job_vacancy,job_specification1,job_specification2,job_specification3,skill_reqired,start_date,special_requirement,contact_person,mobile,wechat,email,where_know,address_upload,apply_time,status) values(
						?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
					)
				",[
					$user_id,
					$name,
					$business_name,
					$address,
					$city,
					$country,
					$postcode,
					$landline,
					$accomodation,
					$distance,
					$company_name,
					$vat_number,
					$job_vacancy,
					$job_Specification1,
					$job_Specification2,
					$job_Specification3,
					$skills_required,
					$start_date,
					$special,
					$contact_person,
					$mobile,
					$wechat,
					$email,
					$where_know,
					$au_path,
					date('Y-m-d H:i:s',time()),
					1
				]
				);
				$insert_result='01';
			
		}
		else
		{
			$this->redirect('index.php/index/index/login');
		}
		$this->assign('insert_result',$insert_result);
		return $this->fetch();
	}
	function privacy_policy()
	{
		return $this->fetch();
	}


	function news_list(){
		$articleres=db('article')->order('id desc')->paginate(3);
        $this->assign('articleres',$articleres);
                return $this->fetch();
	}

	function news_detail(){
		$arid=input('arid');
    	$articles=db('article')->find($arid);
    	db('article')->where('id','=',$arid)->setInc('click');
    	$cates=db('cate')->find($articles['cateid']);
    	$recres=db('article')->where(array('cateid'=>$cates['id'],'state'=>1))->limit(8)->select();
    	$this->assign(array(
          'articles'=>$articles,
          'cates'=>$cates,
          'recres'=>$recres,
    	));
        return $this->fetch('news_detail');
	}
	
	function forget_pwd(){
        return $this->fetch('forget_pwd');
	}

	function change_pwd(){
		return $this->fetch('change_pwd');
	}
}
