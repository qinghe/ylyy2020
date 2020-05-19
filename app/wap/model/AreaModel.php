<?php
namespace app\wap\model;
use think\Model;
use think\Db;

class AreaModel extends Model
{
    protected $name='area';
    
    public function getAreaUrl($area) {
	    $url = '';
	    switch (config('sys.url_model')) {
	    	case '1'://动态
	    		if (config('sys.sys_levelurl') == 'm') {
	    			$url = "/index.php?area=".$area['etitle'];
	    		}else{
	    			$url = "/index.php?s=wap&area=".$area['etitle'];
	    		}
	    		break;
	    	case '3'://伪静态
	    		$urlqz = config('sys.sys_levelurl') == 'm' ? '' : '/m'; //url前缀
	    		$url = $urlqz.'/'.$area['etitle'].".html";
	    		break;
	    }
	    return $url;
	}
}