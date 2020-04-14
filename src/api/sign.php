<?php
define("SIGN_SCRIPT_PATH", "signin/");

set_time_limit(0); //设置脚本执行时间无上限
date_default_timezone_set("Asia/Shanghai"); //设置时区
header("Content-Type: text/html; charset=utf-8"); //设置 UTF-8

$files = scandir(SIGN_SCRIPT_PATH);

//遍历执行
foreach($files as $script)
{
	if($script != "." && $script != "..")
		include(SIGN_SCRIPT_PATH."/".$script); //执行
}

