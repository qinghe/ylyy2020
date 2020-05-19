<?php
namespace app\wap\controller;
use think\Db;

class Tag extends Common
{
	public function index(){
		$input = input();
		$info['title'] = htmlspecialchars($input['title']);
		$tagurl = db('tagurl')->where(["tagurl"=>$info['title']])->find();
    	$info['title'] = $tagurl ? $tagurl['tagname'] : $info['title'];
		$this->assign($info);
		return $this->fetch();
	}
}
?>