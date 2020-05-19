<?php
use think\Db;
//截取字符串
function str2sub($str, $num, $flag = false, $unhtml = true,  $sp = '...') {
    if ($unhtml) {
        $str = str_replace("&nbsp;", '', $str);
        $str = strip_tags($str);
    }
    if ($str == '' || $num <= 0) {
        return $str;
    }
    $strlen = mb_strlen($str, 'utf-8');
    $newstr ='';
    $newstr .= mb_substr($str, 0, $num, 'utf-8');//substr中国会乱码
    if ($num < $strlen && $flag) {
        $newstr .= $sp;
    }
    return $newstr;
}
function redirecturl(){
    $source_url = curPageURL();
    $redirecturl = db('redirecturl')->where(['source_url'=>$source_url])->find();
    if ($redirecturl) {
        header("Location: ".$redirecturl['target_url'], TRUE, 301);
        exit();
    }
}

function curPageURL() 
{
    $pageURL = 'http';
    if ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || (isset($_SERVER["HTTP_FROM_HTTPS"]) && $_SERVER["HTTP_FROM_HTTPS"] == "on")) {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    return $pageURL;
}
//禁用词
function disablewords($con){
    if (config('sys.disablewords_status')) {
        $text = trim(config('sys.disablewords_content'));
        $textlist = explode("\n", $text);
        foreach ($textlist as $k => $v) {
            $jg = strpos($v, "，") ? "，" : ',';
            $ciar = explode($jg, $v);
            $con = str_replace($ciar[0], (isset($ciar[1]) ? $ciar[1] : ''), $con);
        }
    }
    return $con;
}

//记录日志
function writelog($uid,$username,$description,$status)
{
    $data['admin_id'] = $uid;
    $data['admin_name'] = $username;
    $data['description'] = $description;
    $data['status'] = $status;
    $data['ip'] = request()->ip();
    $data['add_time'] = time();
    $log = db('Log')->insert($data);
}

//字符串截取
function str_replace_limit($search, $replace, $subject, $limit=-1) { 
    if (is_array($search)) { 
        foreach ($search as $k=>$v) { 
            $search[$k] = '`' . preg_quote($search[$k],'`') . '`'; 
        }
    }else { 
        $search = '`' . preg_quote($search,'`') . '`'; 
    } 
    return preg_replace($search, $replace, $subject, $limit); 
} 

//删除根据目录删除子文件
function dir_del($dirpath){
    $dh=opendir($dirpath);
    while (($file=readdir($dh))!==false) {
        if($file!="." && $file!="..") {
            $fullpath=$dirpath."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                dir_del($fullpath);
                @rmdir($fullpath);
            }
        }
    }    
    closedir($dh);
    $isEmpty = true;
    $dh=opendir($dirpath);
    while (($file=readdir($dh))!== false) {
        if($file!="." && $file!="..") {
            $isEmpty = false;
            break;
        }
    }
    return $isEmpty;
}

//手机端
function is_mobile()
{
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    }
    /*if (isset ($_SERVER['HTTP_VIA']))  //cdn屏蔽via判断有效
    {
        if(!stristr($_SERVER['HTTP_VIA'], "cache")){
         	return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
    }*/
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
        'oppo','xiaomi','miui','huawei','coolpad','sony','ericsson','mot','samsung',
        'htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo',
        'iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm',
        'operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
        return true;
        }
    }
    if (isset($_SERVER['HTTP_ACCEPT']))
    {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}

//字符转数组
function str2arr($str, $sp = '***') {
    if ($str == '') {
        return $str;
    }
    $strlist = explode($sp, $str);
    foreach ($strlist as $k => $v) {
        if (empty($v)) {
            unset($strlist[$k]);
        }
    }
    return $strlist;
}
//获取字符串图片（1张）
function str2img($str, $num = 1) {
    $pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";  
    $content = $str;  //文章内容  
    preg_match_all($pattern, $content, $matchContent);  

    if (count($matchContent[1]) >= $num) {
        $temp = $matchContent[1][$num -1];
        if (!stristr($temp, 'http')) {
            $temp = config('sys.site_protocol').'://'.config('sys.site_url').$temp;
        }
    }else{
        $temp = "";
    }
    return $temp;
}

/**
 * 修改地区中标签
 * @param  string $str 文字内容 $sys_area 地区名称
 */
function update_str_dq($str, $sys_area = ''){
    $cityname = "";
    $provname = "";
    if ($sys_area) {
        $dbarea = db('area');
        $city = $dbarea->where(['etitle'=>$sys_area])->find();
        $prov = [];
        
        if ($city) {
            if ($city['pid'] == 0) {
                $prov = $city;
            }else{
                $cityname = $city['stitle'];
                $prov = top_aera($city['pid']);
            }
        }
        if ($prov) {
            $provname = $prov['stitle'];
        } 
    }
    if (is_object($str)) {
        $str = $str->toarray();
    }
    if (is_array($str)) {
        foreach ($str as $k111 => $v111) {
            if (is_string($v111)) {
                $v111 =  str_replace('[prov]', $provname, $v111);
                $v111 =  str_replace('[city]', $cityname, $v111);
                $v111 =  str_replace('[prov_or_city]', $cityname ? $cityname : $provname, $v111);
                $str[$k111] = $v111;
            }
        }
    }else{
        $str = str_replace('[prov]', $provname, $str);
        $str = str_replace('[city]', $cityname, $str);
        $str =  str_replace('[prov_or_city]', $cityname ? $cityname : $provname, $str);
    }
    
    return $str;
}

function top_aera($area_id){
    $dbarea = db('area');
    $prov = $dbarea->where(['id'=>$area_id])->find();
    if ($prov['pid'] != 0) {
        $prov = top_aera($prov['pid']);
    }
    return $prov;
}

function build_query($query_data, $encoding = false) {  
    $res = '';  
    $count = count ( $query_data );  
    $i = 0;  
    foreach ( $query_data as $k => $v ) {  
        if ($encoding === true) {  
            $v = urlencode ( $v );  
        }  
        if ($i < $count - 1) {  
            $res .= $k . '=' . $v . '&';  
        } else {  
            $res .= $k . '=' . $v;  
        }  
        $i ++;  
    }  
    return $res;  
} 

//写入配置文件
function setConfigfile($file, $arr){
    $str="<?php \nreturn [\n";
    foreach($arr as $key=>$v){
        $str.= "\t'".$key."'=>'".$v."',\n";
    }
    $str.="];\n";
    file_put_contents($file, $str);
}

function add_slashes_recursive($variable )
{
    if (is_string($variable)){
        $str = stripslashes($variable);
        $str = preg_replace( "@<script(.*?)</script>@is", "", $str );
        $str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str );
        return addslashes($str);
    }elseif(is_array($variable)){
        foreach( $variable as $i => $value ){
            $variable[add_slashes_recursive($i)] = add_slashes_recursive($value) ;
        }
    }
    return $variable ;
}
function getprovince($areaid){
    $areadata = db('area')->where(['id'=>$areaid])->find();
    if ($areadata) {
        if ($areadata['pid'] == 0) {
            return $areaid;
        }else{
            return getprovince($areadata['pid']);
        }
    }
}
?>