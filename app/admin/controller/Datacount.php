<?php
namespace app\admin\controller;
use app\admin\model\MediaModel;
use think\Config;
use think\Db;
use think\Session;
use think\Request;

class Datacount extends Common
{
	//百度统计
    public function baidutj() {
        return $this->fetch();
    }

    //主词监控
    public function pmjkhq() {
        $this->cloud = new \com\Cloud(config('cloud.identifier'));
        $html_status = url_get_contents($this->cloud->mainUrl()."/main.html");
        $html_status = $html_status == 'SUCCESS' ? 1 : 0;
        //主词排名监控获取
        $mainkeywords = null;
        if ($html_status && config('sys.api_pmjkhq')) {
            $mainkeywords = send_post($this->cloud->yunapiUrl().'/getmainkeywords', ['domain'=>config('sys.site_levelurl')]);
            $mainkeywords = $mainkeywords ? json_decode($mainkeywords, true) : null;
            if ($mainkeywords) {
                $mainkeywords = $mainkeywords['state'] == 1 ? $mainkeywords['data'][0] : null;
            }
        }
        
        $this->assign([
            'mainkeywords' => $mainkeywords,
        ]);
        return $this->fetch();
    }

}
