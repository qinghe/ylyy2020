<?php
namespace app\admin\controller;
use app\admin\model\TagurlModel;
use think\Db;

class Tagurl extends Common
{
    public function index(){
        $tagurl = new TagurlModel();
        $infolist = $tagurl->getAllTagurl(); 
        $this->assign('infolist', $infolist);
        return $this->fetch();
    }

    public function addTagurl()
    {
        if(request()->isAjax()){
            $param = input('post.');
            $tagurl = new TagurlModel();
            $flag = $tagurl->insertTagurl($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }

    public function editTagurl()
    {
        $tagurl = new TagurlModel();
        if(request()->isAjax()){
            $param = input('post.');
            $flag = $tagurl->editTagurl($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $info = $tagurl->getOneTagurl($id);
        $this->assign('info', $info);
        return $this->fetch();
    }

    public function delTagurl()
    {
        $id = input('param.ids');
        $tagurl = new TagurlModel();
        $flag = $tagurl->delTagurl($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}
