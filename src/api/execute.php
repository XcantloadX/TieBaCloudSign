<?php
include_once "../global.php";
include_once __LIB__."/log.php";
define("SIGN_SCRIPT_PATH", __ROOT__."/signer/");

set_time_limit(0); //设置脚本执行时间无上限
date_default_timezone_set("Asia/Shanghai"); //设置时区


$type = isset($_GET["type"]) ? $_GET["type"] : "html";
if($type == "text")
	header("Content-Type: text/plain; charset=utf-8");
else
{
	header("Content-Type: text/html; charset=utf-8");
	logAsHtml(true);
}

//获取所有签到脚本
$files = scandir(SIGN_SCRIPT_PATH);

//遍历执行
foreach($files as $file)
{
	if($file != "." && $file != ".." && strpos($file, ".php") > 0)
		include(SIGN_SCRIPT_PATH."/".$file);
}