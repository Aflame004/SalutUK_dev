<?php
namespace Hr_const;//存放各类配置常量

class Hr_const
{
	//加密盐分
	const salt='*$uU4P#2lxx*xalsifoasdvx8alNM~';
	//主域名
	const domain='https://jobdemo.sprmint.cn';
	//邮箱验证接口
	const validate_email='/index.php/index/index/validate_email';
	//邮箱忘记密码
	const change_pwd_email='/index.php/index/index/change_pwd';
	//成功返回值
	const success='01';
	//失败返回值
	const fail='00';
	//账号冻结返回值
	const freeze='02';
	//微信appid
	const wechat_appid='wx739c720b8820bf2d';
	//微信secretid
	const wechat_secretid='5301271f7c87d3da5ff4611b7e4d8a6a';
	//facebook appid
	const fb_appid='292728791534825';
	//facebook secretid
	const fb_secretid='51aa63bc733941028c43630d5eef2b57';
}

?>