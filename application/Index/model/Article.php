<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Article extends Model
{
	// 文章数据表关联栏目数据表
  public function cate(){
  	return $this->belongsTo('cate','cateid');

  }
	
}
