<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:9:"./404.htm";i:1508399638;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <title>404</title>
    <link rel="stylesheet" type="text/css" href="/statics/layui/css/layui.css">
    <style type="text/css">
        body {
            line-height: 1.6;
        }
        .page-404 {
            color: #afb5bf;
            padding-top: 60px;
            padding-bottom: 90px;
        }
        .text-c {
            text-align: center;
        }
        .ml-20 {
            margin-left: 20px;
        }
        .va-m {
            vertical-align: middle!important;
        }
        .page-404 .error-title {
            font-size: 80px;
        }
        .page-404 .error-description {
            font-size: 24px;
        }
        .page-404 .error-info {
            font-size: 14px;
        }
        .c-primary{
            color:#5a98de
        }
        .c-primary:hover{
            text-decoration: underline;
            color:#5a98de
        }
        p {
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <div class="page-404 text-c">
        <p class="error-title">
            <span class="va-m"> 404</span>
        </p>
        <p class="error-description">不好意思，您访问的页面不存在~</p>
        <p class="error-info">您可以：
            <a href="javascript:;" onclick="history.go(-1)" class="c-primary">&lt; 返回上一页</a>
            <span class="ml-20">|</span>
            <a href="/" class="c-primary ml-20">去首页 &gt;</a>
            <span class="ml-20">|</span>
            <a href="http://www.yunucms.com/Help/index.html" target="_blank" class="c-primary ml-20">寻求帮助 &gt;</a>
        </p>
    </div>
</body>
</html>
