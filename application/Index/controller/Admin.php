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
use app\index\model\Article as ArticleModel;


class Admin extends Controller
{
	public function myinfo()
	{
		return phpinfo();
	}
    public function index()//选择默认跳转页面
    {
		$this->check_auth();
		$this->redirect('/index.php/index/admin/match');
		$admin_page=null;
		$admin_email=Session::get('admin_email');
		$result=Db::query('select admin_group from user where email=?',[$admin_email]);
		$admin_group=$result[0]['admin_group'];
		$result=Db::query('select * from admin_group where group_id=?',[$admin_group]);
		$auth_id=$result[0]['auth_id'];
		switch($auth_id)
		{
			case 1:
				$admin_page='account';
				break;
			case 2:
				$admin_page='';
				break;
			case 3:
				$admin_page='';
				break;
			case 4:
				$admin_page='match';
				break;
			case 5:
				$admin_page='';
				break;
			case 6:
				$admin_page='admin_admin';
				break;
			case 7:
				$admin_page='person_verify';
				break;
		}
	}
	public function login()
	{
		return $this->fetch();
	}
	public function logout()
	{
		Session::clear();
		$this->redirect('/index.php/index/admin/login');
	}
	public function modify_pwd(){
		return $this->fetch();
	}
	public function person_verify()
	{
		if($this->check_auth())
		{
			$result=DB::query('select * from user_info where verification_status=0');
			$this->assign('user_info',$result);
			return $this->fetch();
		}
	}
	public function person_verify_detail()
	{
		if($this->check_auth())
		{
			$request = Request::instance();
			$user_id=$request->param("id");
			$result=DB::query('select * from user_info where user_id=?',[$user_id]);
			$this->assign('user_info',$result);
			return $this->fetch();
		}
	}
	public function match()
	{
		if($this->check_auth())
		{
			$result=DB::query('select * from job_publishment where status=1');
			$this->assign('result',$result);
			return $this->fetch();
		}
	}
	public function match_next()
	{
		if($this->check_auth())
		{
			$request = Request::instance();
			$id=$request->param("id");
			$result=DB::query('select * from job_publishment where id=?',[$id]);
			$start_date=$result[0]['start_date'];
			$job_vacancy=$result[0]['job_vacancy'];
			$job_specification1=$result[0]['job_specification1'];
			$job_specification2=$result[0]['job_specification2'];
			$job_specification3=$result[0]['job_specification3'];
			
			$result1=DB::query('select * from job_hunting where specify=? and status=1',[$job_vacancy]);
			$result2=DB::query('select * from job_hunting where specify<>? and status=1',[$job_vacancy]);
			$result=array_merge($result1,$result2);
			$this->assign('result',$result);
			$this->assign('job1',$id);//job_publishment_id
			return $this->fetch();
		}
	}
	public function match_list()
	{
		if($this->check_auth())
		{
			$result=DB::query('select * from job_match order by status');
			$result_array=array();
			foreach($result as $item)
			{
				$job_publishment_id=$item['job_publishment_id'];
				$job_hunting_id=$item['job_hunting_id'];
				$publishment=DB::query('select * from job_publishment where id=?',[$job_publishment_id]);
				$hunting=DB::query('select * from job_hunting where id=?',[$job_hunting_id]);
				$temp=array(
					"match_id"=>$item['id'],
					"employer_name"=>$publishment[0]['name'],
					"employer_landline"=>$publishment[0]['landline'],
					"employer_job_vacancy"=>$publishment[0]['job_vacancy'],
					"hunting_name"=>$hunting[0]['first_name'].' '.$hunting[0]['surname'],
					"hunting_mobile"=>$hunting[0]['mobile_district'].'-'.$hunting[0]['mobile'],
					"status"=>$item['status'],
				);
				array_push($result_array,$temp);
			}
			$this->assign("result",$result_array);
			return $this->fetch();
		}
	}
	function adminaccount()
	{
		$this->check_auth();
		$request = Request::instance();
		
	}
	function admin_admin()//管理管理员用户
	{
		$this->check_auth();
		$request = Request::instance();
		$result=DB::query('select user_id,email,admin_group from user where role=3');
		for($i=0;$i<count($result);$i++)
		{
			$user_id=$result[$i]['user_id'];
			$temp=DB::query('select * from admin_info where user_id=?',[$user_id]);
			$result[$i]['mobile']=$temp[0]['mobile'];
			$result[$i]['name']=$temp[0]['name'];
			$temp=DB::query('select * from admin_group_describe where group_id=?',[$result[$i]['admin_group']]);
			$result[$i]['admin_group']=$temp[0]['group_name'];
		}
		$group=DB::query('select * from admin_group_describe');
		$this->assign('result',$result);
		$this->assign('groups',$group);
		return $this->fetch();
	}


	function admin_admin_group()
	{
		$this->check_auth();
		$request = Request::instance();
		$result=Db::query('select * from admin_group_describe');
		$gourp_item=Db::query('select * from auth_item');
		$this->assign('result',$result);
		$this->assign('group_item',$gourp_item);
		return $this->fetch();
	}
	function company_verify()
	{
		$this->check_auth();
		$request = Request::instance();
		$result=Db::query('select a.*,b.user_name,b.phone from
						(select * from company_info where status=0) a
						join
						(select user_id,user_name,phone from user_info) b
						on
						a.user_id=b.user_id
						order by apply_time desc;');
		$this->assign('result',$result);
		return $this->fetch();
	}
	
	function company_verify_detail()
	{
		$this->check_auth();
		$request = Request::instance();
		$company_id=$request->param('company_id');
		$result=Db::query('select a.*,b.user_name,b.phone from
						(select * from company_info where company_id=?) a
						join
						(select user_id,user_name,phone from user_info) b
						on
						a.user_id=b.user_id',[$company_id]);
		$this->assign('result',$result[0]);
		return $this->fetch();
	}
	function account()
	{
		$this->check_auth();
		$request = Request::instance();
		$user_name = $request->param('user_name');
		$result=null;
		if(empty($user_name))
		{
			$result=Db::query('select * from
							(select * from user) a
							join
							(select * from user_info) b
							on a.user_id=b.user_id
							order by regist_time');
		}
		else
		{
			$result=Db::query('select * from
							(select * from user) a
							join
							(select * from user_info) b
							on a.user_id=b.user_id
							where user_name=?
							order by regist_time',[$user_name]);
		}
		
		$this->assign('result',$result);
		return $this->fetch();
	}
	public function account_info_edit()
	{
		$this->check_auth();
		$request = Request::instance();
		$user_id = $request->param('user_id');
		$result=Db::query('select * from user_info where user_id=?',[$user_id]);
		$this->assign('result',$result[0]);
		$role_res=Db::query('select role from user where user_id=?',[$user_id]);
		$this->assign('account_role',$role_res[0]);
		return $this->fetch();
	}

	public function news_list(){
		// 输出数组信息
		$list = ArticleModel::paginate(5); 
		$this->assign('list',$list);

	 return $this->fetch();
	}

	public function news_add()
    {     
      //获取提交的信息
    	if(request()->isPost()){ 
        // dump($_POST);die;
    		$data=[
    			'title'=>input('title') ,              
    			'author'=>input('author'),
                'desc'=>input('desc'),
                'keywords'=>str_replace('，',',',input('keywords')),
                'cateid'=>input('cateid'),
                'content'=>input('content'),
                'time'=>time(),
                'state'=>input('state'),
                'pic'=>input('pic'),
                'photo'=>input('photo[]'),
    		];
            if(input('state')=='on'){
                $data['state']=1;
			}
			// if(input('pic')===NULL){
			// 	$this->error('请上传缩略图');
			// }
            // 图片上传
            if($_FILES['pic']['tmp_name']){
                $file=request()->file('pic');
                $info = $file->move(ROOT_PATH .'public' . DS . 'static/uploads');
                $data['pic']='/static/uploads/'.$info->getSaveName();
            }  
              // 图片上传
           $files = request()->file('photo');  
           $date=date("Y-m-d",time());//已上传日期为子目录名
         $saveName=time().rand(1111,9999);
        // foreach($files as $file){
        //     // 移动到框架应用根目录/public/uploads/ 目录下
         
        //     $info = $file->move(ROOT_PATH . 'public' . DS . 'static/uploads');
        //     if($info){
        //         // 成功上传后 获取上传信息
        //         // 输出 jpg
        //         echo $info->getExtension();
        //           $data['photo']='/static/uploads/'.$date."/".$saveName;
        //         // 输出 42a79759f284b767dfcb2a0197904287.jpg
        //         echo $info->getFilename();
        //     }else{
        //         // 上传失败获取错误信息
        //         echo $file->getError();
        //     }
             
        // }
             // 验证提交的信息
			$validate = \think\ Loader::validate('Article');
			// 显示错误信息
			if(!$validate->scene('add')->check($data)) {
			$this->error($validate->getError());
			die;
			}
   // 添加到数据库
		if(Db::name('Article')->insert($data)){
    		return $this->success('添加成功','news_list');
    	}
    	else{
    		return  $this->error('添加失败');
    	}
    	return;
    }
    $cateres=db('cate')->select();
    $this->assign('cateres',$cateres);
        return $this->fetch('');

	}
	

	 //管理员修改 
		public function news_edit(){
			$id=input('id');
		$article=db('article')->find($id);
		if(request()->isPost()){
			$data=[
				'id'=>input('id'), 
				'title'=>input('title'),       
				'author'=>input('author'), 
				'desc'=>input('desc'),
				'keywords'=>str_replace('，', ',', input('keywords')),
				'content'=>input('content'),
				'cateid'=>input('cateid'),
			];
			if(input('state')=='on'){
				$data['state']=1;
			}else{
				$data['state']=0;
			}

			if($_FILES['pic']['tmp_name']){
				$file = request()->file('pic');
				$info = $file->move(ROOT_PATH . 'public' . DS . 'static/uploads');
				$data['pic']='/static/uploads/'.$info->getSaveName();
			}
			$validate = \think\Loader::validate('article');
			if(!$validate->scene('edit')->check($data)){
			$this->error($validate->getError()); die;
			}
			if(db('article')->update($data)){
				$this->success('修改文章成功！','news_list');
			}else{
				$this->error('修改文章失败！');
			}
			return;
		}
		$this->assign('article',$article);
		$cateres=db('cate')->select();
		$this->assign('cateres',$cateres);
		return $this->fetch();
	}

   // 删除操作
	public function news_del(){
		$id=input('id');
		if($id){
			if(db('article')->delete(input('id'))){
				$this->success('删除文章成功！','news_list');
			}else{
				$this->error('删除文章失败！');
			}
		}
		
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
