<?php
namespace app\index\controller;

use think\Db;

class Index extends Common
{
    public function index()
    {
        //记录浏览
        $browse = array(
            'ip' => request()->ip(),
            'time' => time(),
            'type' => is_mobile() ? 2 : 1,
        );

        db('browse')->insert($browse);
        if (config('sys.site_guide') == 1 && request()->url() == "/") {
            return $this->fetch($this->tpl_file.'index_default.html');
        }

        if (config('sys.indexhtml')) {
            if (!session('sys_areainfo')) {
                $indexstr = "./index.html";
                $dir = "./";
                $htmlfilename = "index";
            } else {
                $indexstr = "./caches/area/".config('sys.sys_area').".html";
                $dir = "./caches/area/";
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $htmlfilename = config('sys.sys_area');
            }
            if (file_exists($indexstr)) {
                if ((date('d', filemtime($indexstr)) != date('d', time())) && (config('sys.indexhtml_time') < date('H:i:s', time()))) {
                    unlink($indexstr);
                } else {
                    return $this->fetch($indexstr);
                    exit();
                }
            }
            $tpl_file = './template/'.config('sys.theme_style').'/index/';
            $info = $this->buildHtml($htmlfilename, $dir, $tpl_file.'index_index.html');
        }
        return $this->fetch();
    }

    public function empty404()
    {
        return $this->fetch("./404.htm");
    }

    private function buildHtml($htmlfile='', $htmlpath='', $templateFile='')
    {
        $content = $this->fetch($templateFile);
        $htmlpath = !empty($htmlpath) ? $htmlpath : APP_PATH.'/html';
        $htmlfile = $htmlpath.$htmlfile.'.html';
        if (file_put_contents($htmlfile, $content) === false) {
            return false;
        } else {
            return true;
        }
    }
}
