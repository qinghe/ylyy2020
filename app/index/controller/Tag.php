<?php
namespace app\index\controller;
use think\Db;

class Tag extends Common
{
	public function index(){
		$input = input();
		$info['title'] = htmlspecialchars($input['title']);
		$info['title'] = str_replace(".html", "", $info['title']);
		$taginfo = db('tagurl')->where(["tagurl"=>$info['title']])->find();
    	$info['title'] = $taginfo ? $taginfo['tagname'] : $info['title'];
		$this->assign($info);
		$this->assign(['tag'=>$taginfo]);
		return $this->fetch();
	}
}
?>