<?php
namespace app\admin\controller;
use app\admin\model\AreaModel;
use app\admin\model\MediaModel;
use think\Config;

class System extends Common
{
    public function basic()
    {
        $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
        if(request()->isAjax()){
            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param = input('post.');  
            unset($param['file']);
            unset($param['ico']);
            if ($conflist['login_url'] != $param['login_url']) {
                if (file_exists($conflist['login_url'])) {
                    if(!rename($conflist['login_url'], $param['login_url'])){
                        return json(['code' => 0, 'data' => '', 'msg' => '后台路径文件修改失败，请检查根目录权限！']);
                        exit();
                    }
                }else{
                    return json(['code' => 0, 'data' => '', 'msg' => '后台路径文件不存在请检查！']);
                    exit();
                }
            }
            $param['site_guide'] = array_key_exists("site_guide", $param) ? 1 : 0;
            $param['site_slide'] = array_key_exists("site_slide", $param) ? 1 : 0;

            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));
            return json(['code' => 1, 'data' => '', 'msg' => '更新设置成功']);
            exit();
        }

        $indexdef = getFileFolderList('./template/'.config('sys.theme_style').'/index' , 2, 'index_default.html');
        $this->assign('indexdef', $indexdef);
        return $this->fetch();
    }

    //生成站点地图
    public function sitemap() {
        $api = new Api();
        $api->createSitemap();
        
        return $this->fetch();
    }

    public function seo()
    {
        $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
        if(request()->isAjax()){
            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param = input('post.');  
            $param['seo_area'] = array_key_exists("seo_area", $param) ? 1 : 0;

            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));

            return json(['code' => 1, 'data' => '', 'msg' => '更新设置成功']);
            exit();
        }
        $area = new AreaModel();
        $nav = new \org\Leftnav;
        $arr = $area->getAllArea(['isopen'=>1,'istop'=>1]);
        $arealist = $nav::rule($arr);
        $this->assign('arealist', $arealist);
        return $this->fetch();
    }

    public function qiniu()
    {
        $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
        if(request()->isAjax()){
            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param = input('post.');  
            $param['qiniu'] = array_key_exists("qiniu", $param) ? 1 : 0;

            if (strpos($param['qiniu_accesskey'], '*')) {
                unset($param['qiniu_accesskey']);
            }
            if (strpos($param['qiniu_secretkey'], '*')) {
                unset($param['qiniu_secretkey']);
            }

            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));

            return json(['code' => 1, 'data' => '', 'msg' => '更新设置成功']);
            exit();
        }
        $qiniu_accesskey = config('sys.qiniu_accesskey');
        if ($qiniu_accesskey) {
            $qiniu_accesskey = substr_replace($qiniu_accesskey, '********', 4, count($qiniu_accesskey)-5);
        }
        $qiniu_secretkey = config('sys.qiniu_secretkey');
        if ($qiniu_accesskey) {
            $qiniu_secretkey = substr_replace($qiniu_secretkey, '********', 4, count($qiniu_secretkey)-5);
        }
        $this->assign([
            'qiniu_accesskey' => $qiniu_accesskey,
            'qiniu_secretkey' => $qiniu_secretkey
        ]);
        return $this->fetch();
    }

    public function interapi()
    {
        $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
        if(request()->isAjax()){
            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param = input('post.');

            $param['api_bdts'] = array_key_exists("api_bdts", $param) ? 1 : 0;
            $param['api_bdxzh'] = array_key_exists("api_bdxzh", $param) ? 1 : 0;
            $param['api_bdqc'] = array_key_exists("api_bdqc", $param) ? 1 : 0;
            $param['api_wyc'] = array_key_exists("api_wyc", $param) ? 1 : 0;
            $param['api_xxts'] = array_key_exists("api_xxts", $param) ? 1 : 0;
            $param['api_pmjkhq'] = array_key_exists("api_pmjkhq", $param) ? 1 : 0;
            $param['api_media'] = array_key_exists("api_media", $param) ? 1 : 0;
            $param['api_autologin'] = array_key_exists("api_autologin", $param) ? 1 : 0;
            $param['api_bdautots'] = array_key_exists("api_bdautots", $param) ? 1 : 0;

            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));

            return json(['code' => 1, 'data' => '', 'msg' => '更新设置成功']);
            exit();
        }
        $issq = 0;
        if (config('cloud.identifier')) {
            $issq = config('cloud.grant') ? 1 : 0;
        }
        $this->assign('issq', $issq);
        return $this->fetch();
    }
    public function listrows()
    {
        $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
        if(request()->isAjax()){
            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param['admin_list_rows'] = input('list_rows');

            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));

            return json(['code' => 1, 'data' => '', 'msg' => '更新成功']);
            exit();
        }
    }
    
    public function copyright(){
    	$iscopy = false;
    	$this->cloud = new \com\Cloud(config('cloud.identifier'));
	    $html_status = url_get_contents("http://www.baidu.com");
        $html_status = $html_status ? 1 : 0;
        $cloudstr = "Tongxin Error";
        if ($html_status) {
            if (config('cloud.identifier')) {
                $iscopy = config('cloud.grant') ? true : false;
            }
            $this->cloud->record(config('sys.site_title'), config('yunucms.version'))->api('recordapi');
        }
        if ($iscopy) {
        	echo urldecode("%e7%89%88%e6%9d%83%e7%94%b3%e6%98%8e%ef%bc%9a%e5%bd%93%e5%89%8d%e7%ab%99%e7%82%b9%e4%bd%bf%e7%94%a8%e4%ba%91%e4%bc%98%e4%bc%81%e4%b8%9a%e7%bd%91%e7%ab%99%e7%ae%a1%e7%90%86%e7%b3%bb%e7%bb%9f%e6%90%ad%e5%bb%ba%ef%bc%8c%e8%bd%af%e4%bb%b6%e8%91%97%e4%bd%9c%e6%9d%83%e7%99%bb%e8%ae%b0%e5%8f%b7%ef%bc%9a2018SR118857%ef%bc%8c%e7%bb%8f%e4%ba%91%e4%bc%98%e6%8e%88%e6%9d%83%e5%b9%b3%e5%8f%b0%e9%aa%8c%e8%af%81%ef%bc%8c%e5%bd%93%e5%89%8d%e7%ab%99%e7%82%b9%e5%9f%9f%e5%90%8d".config('sys.site_url')."%e5%b7%b2%e8%8e%b7%e5%be%97%e4%ba%91%e4%bc%98%e5%ae%98%e6%96%b9%e5%90%88%e6%b3%95%e6%8e%88%e6%9d%83%e4%b8%ba%e6%ad%a3%e7%89%88%e7%94%a8%e6%88%b7%ef%bc%8c%e5%8f%97%e6%b3%95%e5%be%8b%e4%bf%9d%e6%8a%a4%e3%80%82");
        }else{
        	echo urldecode("%e7%89%88%e6%9d%83%e7%94%b3%e6%98%8e%ef%bc%9a%e5%bd%93%e5%89%8d%e7%ab%99%e7%82%b9%e4%bd%bf%e7%94%a8%e4%ba%91%e4%bc%98%e4%bc%81%e4%b8%9a%e7%bd%91%e7%ab%99%e7%ae%a1%e7%90%86%e7%b3%bb%e7%bb%9f%e6%90%ad%e5%bb%ba%ef%bc%8c%e8%bd%af%e4%bb%b6%e8%91%97%e4%bd%9c%e6%9d%83%e7%99%bb%e8%ae%b0%e5%8f%b7%ef%bc%9a2018SR118857%ef%bc%8c%e7%bb%8f%e4%ba%91%e4%bc%98%e6%8e%88%e6%9d%83%e5%b9%b3%e5%8f%b0%e9%aa%8c%e8%af%81%ef%bc%8c%e5%bd%93%e5%89%8d%e7%ab%99%e7%82%b9%e5%9f%9f%e5%90%8d".config('sys.site_url')."%e6%9c%aa%e8%8e%b7%e5%be%97%e4%ba%91%e4%bc%98%e5%ae%98%e6%96%b9%e5%90%88%e6%b3%95%e6%8e%88%e6%9d%83%e4%b8%ba%e7%9b%97%e7%89%88%e7%94%a8%e6%88%b7%ef%bc%8c%e5%ad%98%e5%9c%a8%e4%be%b5%e6%9d%83%e9%a3%8e%e9%99%a9%e3%80%82");
        }
    }

    public function checkPmjkhq()
    {
        $this->cloud = new \com\Cloud();
        $mainkeywords = send_post($this->cloud->yunapiUrl().'/getmainkeywords', ['domain'=>config('sys.site_levelurl')]);
        $mainkeywords = $mainkeywords ? json_decode($mainkeywords, true) : null;
        if ($mainkeywords) {
            $mainkeywords = $mainkeywords['state'] == 1 ? $mainkeywords['data'][0] : null;
        }
        $data['state'] = $mainkeywords ? 1 : 0;
        return json_encode($data);
    }
    public function checkMedia()
    {
        if(request()->isAjax()){
            $apikey = input('apikey'); 
            $media = new MediaModel(); 
            $domainid = $media->checkmedia($apikey);
            $data['state'] = $domainid ? 1 : 0;
            return json_encode($data);
        }
    }
    public function upload()
    {
        $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
        if(request()->isAjax()){
            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param = input('post.'); 
            $fileext = strtolower($param['upload_file_ext']);
            $imageext = strtolower($param['upload_image_ext']);
            if (strpos($fileext, 'asp') !== false || strpos($fileext, 'php') !== false) {
                return json(['code' => 0, 'data' => '', 'msg' => '禁止使用程序格式后缀']);
                exit();
            }
            if (strpos($imageext, 'asp') !== false || strpos($imageext, 'php') !== false) {
                return json(['code' => 0, 'data' => '', 'msg' => '禁止使用程序格式后缀']);
                exit();
            }

            unset($param['file']);
            unset($param['filefont']);
            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));
            return json(['code' => 1, 'data' => '', 'msg' => '更新设置成功']);
            exit();
        }
        return $this->fetch();
    }

    public function disablewords()
    {
        $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
        if(request()->isAjax()){
            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param = input('post.');  
            $param['disablewords_status'] = array_key_exists("disablewords_status", $param) ? 1 : 0;
            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));
            return json(['code' => 1, 'data' => '', 'msg' => '更新设置成功']);
            exit();
        }

        $indexdef = getFileFolderList('./template/'.config('sys.theme_style').'/index' , 2, 'index_default.html');
        $this->assign('indexdef', $indexdef);
        return $this->fetch();
    }

    public function copy(){
        if (request()->isPost()) {
            $coffile = CONF_PATH.DS.'extra'.DS.'sys.php';
            
            $copy_title = input('post.copy_title/s');
            $copy_name = input('post.copy_name/s');
            $copy_url = input('post.copy_url/s');

            Config::load($coffile, '', 'sys');
            $conflist = Config::get('','sys');
            $param = input('post.');  

            setConfigfile($coffile, add_slashes_recursive(array_merge($conflist, $param)));

            return json(['code' => 1, 'data' => '', 'msg' => '更改后台版权成功']);
            exit();
        }
    }
}
