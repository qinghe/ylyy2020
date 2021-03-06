<?php
namespace app\wap\controller;
use think\Controller;
use think\Config;

class Common extends Controller{
 	public function _initialize()
    {

    	$lock = 'data/install.lock';
        if(!is_file($lock)){
            $this->redirect('/index.php?s=index/install/index');
        }
        
        redirecturl();//301
        error_reporting(E_ERROR);
        if (config('app_debug')) {
            error_reporting(E_ALL);
        }
        
    	$module     = strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());
        $this->tpl_file = './template/'.config('sys.theme_style').'/'.$module.'/';
        //城市
        $area = '';
        if (input('area')) {
        	$area = input('area');
            $areadata = db('area')->where(['etitle'=>$area])->find();
            if (!$areadata) {
                abort(404);
            }/*else{
                if($areadata['isurl']){
                    abort(404);
                }
            }*/
        }

        $levelurl = "";
        if ($_SERVER['HTTP_HOST'] != config('sys.site_url')) {
            $levelurl = str_replace(config('sys.site_levelurl'), '', $_SERVER['HTTP_HOST']);
            if ($levelurl != '') {
            	$levelurl = str_replace('.', '', $levelurl);
            	$area = $levelurl != 'm' ? $levelurl : $area;
                if ($levelurl != 'm') {
                    $areadata = db('area')->where(['etitle'=>$area,'isurl'=>1])->find();
                    if (!$areadata) {
                        abort(404);
                    }
                }
            }
        }

        if (config('sys.wap_levelurl') == 1) {
            if ($levelurl == "") {
                abort(404);
                exit();
            }
        }else{
            if ($levelurl == "m") {
                abort(404);
                exit();
            }
        }
        
        if (!$area && config('sys.seo_default_area')) {
            $defaultarea = db('area')->where(['id'=>config('sys.seo_default_area')])->value('etitle');
            if ($defaultarea) {
                $area = $defaultarea;
            }
        }

        if ($area) {
            $areainfo = db('area')->where(['etitle'=>$area])->find();
            if ($areainfo && !$areainfo['isopen']) {
                abort(404);
            }
            $this->area = $area;
            config('sys.sys_area', $area);
            session('sys_area', $area);
            session('sys_areainfo', $areainfo);
        }else{
            config('sys.sys_area', null);
            session('sys_area', null);
            session('sys_areainfo', null);
        }
        config('sys.sys_levelurl', $levelurl);
        session('sys_levelurl', $levelurl ? $levelurl : null);
        if (!is_mobile() && config('sys.pc_auto')) {
            $pcurl = get_pcurl($_SERVER['REQUEST_URI']);
            $this->redirect($pcurl);
        }
        if (is_dir(RUNTIME_PATH.'temp'.DS)) {//分站开启，自动清除缓存
            $path = RUNTIME_PATH.'temp'.DS;
            dir_del($path);
        }
        
    }
}