<?php
namespace app\index\controller;
use app\index\model\ContentModel;
use app\index\model\CategoryModel;
use think\Db;

class Show extends Common
{
	public function index(){
		$id = input('id');
		$etitle = input('etitle', '', 'trim');
		$ctitle = input('ctitle', '', 'trim');
		$cw = input('cw', '', 'trim');

		if ($ctitle) {
			$categoryinfo = db('category')->where(['etitle'=>$ctitle])->find();
			if (!$categoryinfo) {
				abort(404);
				exit();
			}
		}
		if (is_numeric($etitle)) {
			$id = (int)$etitle;
		}
		if (empty($id) && empty($etitle)) {
			$this->error('参数错误');
			exit();
		}
		if ($etitle) {
			$where = ['etitle'=>$etitle];
		}
		if ($id) {
			$where = ['id'=>$id];
		}

		$content = db('content')->where($where)->find();
		if (empty($content)) {
			abort(404);
			exit();
		}
		$content['ys_title'] = $content['title'];//记录原始title
		$conmodel = new ContentModel();
		$content = $conmodel->getContentByCon($content);
		if ($cw && config("sys.seo_cwkeyword")) {
			$encode = mb_detect_encoding($cw, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5')); 
			$cw = mb_convert_encoding($cw, 'UTF-8', $encode);
			$iscwok = true;
			$cwlist = explode(",", config("sys.seo_cwkeyword"));
			foreach ($cwlist as $k => $v) {
				if (strstr($cw, $content['title'].$v)) {
					$iscwok = false;
				}
			}
			if ($iscwok) {
				abort(404);
				exit();
			}
		}
		//非正常独立内容链接不显示
	    if ($content['area'] != '') {
	    	$area = session('sys_areainfo');
	    	if ($area) {
	    		if (!strstr($content['area'], ','.$area['id'].',')) {
		    		$this->error('当前内容所属区域不正确，无法显示');
					exit();
		    	}
	    	}else{
	    		if (!strstr($content['area'], ',88888888,')) {
	    			$this->error('当前内容所属区域不正确，无法显示');
					exit();
	    		}
	    	}
	    }

		db('content')->where(['id'=>$content['id']])->setInc('click');//增加浏览
		$catemodel = new CategoryModel();
		$category = $catemodel->getOneCategory($content['cid']);

		if (empty($category)) {
			abort(404);
			exit();
		}
		if ($category['tpl_show'] == '') {
			$this->error('模版不存在');
			exit();
		}

		$content = $conmodel->getContentArea($content);
		if ($cw !== '') {
			if (is_numeric($cw)) {
				$cwkey = explode(',', config('sys.seo_cwkeyword'));
				$content['title'] = $content['title'].$cwkey[$cw];
			}else{
				$content['title'] = $cw;
			}
		}
		
		$prev = $conmodel->getContentPrev($category['id'], $content['id']);
		$next = $conmodel->getContentNext($category['id'], $content['id']);

		$content['prev'] = $prev['infostr'];
		$content['prevurl'] = $prev['infourl'];
		$content['prevtitle'] = $prev['infotitle'];

		$content['next'] = $next['infostr'];
		$content['nexturl'] = $next['infourl'];
		$content['nexttitle'] = $next['infotitle'];

		$content = update_str_dq($content, config('sys.sys_area'));

		//{标题名称}
		foreach ($content as $k1 => $v1) {
            $content[$k1] = str_replace('{标题名称}', $content['title'], $v1);;
        }
		$this->assign([
			'content' => $content,
			'category' => $category,
			'cid' => $category['id'],
			'parent' => $category['pid'] != 0 ? $catemodel->getOneCategory($category['pid']): null
		]);

		return $this->fetch($this->tpl_file.$category['tpl_show']);
	}
}

?>