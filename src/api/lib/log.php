<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<?php
define("MAX_LOG_SIZE", 2 * 1024 * 1024);
define("LOG_PATH", $_SERVER['DOCUMENT_ROOT']."/api/stats/signinStats.log");
date_default_timezone_set("Asia/Shanghai"); //设置时区

//判断 log 文件大小
if(file_exists(LOG_PATH) && filesize(LOG_PATH) >= MAX_LOG_SIZE)
	$fp = fopen(LOG_PATH, "w");
else
	$fp = fopen(LOG_PATH, "a");


function logInfo($msg)
{
	output("[Info][".date("Y-m-d h:i:s",time())."] ".$msg);
}

function logError($msg)
{
	output("[Error][".date("Y-m-d h:i:s",time())."] ".$msg);
}

function logWarn($msg)
{
	output("[Warning][".date("Y-m-d h:i:s",time())."] ".$msg);
}

function output($str)
{
	global $fp;
	//输出到文件
	fputs($fp, $str.PHP_EOL);
	//输出到网页
	echo $str.PHP_EOL."<br/>";
	
	//刷新缓冲区
	ob_flush();
	flush();
}
