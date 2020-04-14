<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<?php
define("MAX_LOG_SIZE", 2 * 1024 * 1024);
define("LOG_PATH", $_SERVER['DOCUMENT_ROOT']."/api/stats/signinStats.log");
date_default_timezone_set("Asia/Shanghai"); //设置时区

$name = "default";

//判断 log 文件大小
if(file_exists(LOG_PATH) && filesize(LOG_PATH) >= MAX_LOG_SIZE)
	$fp = fopen(LOG_PATH, "w");
else
	$fp = fopen(LOG_PATH, "a");

//设置输出备注名字
function logSetName($str)
{
	global $name;
	$name = $str;
}

function logInfo($msg)
{
	global $name;
	output("Info", $name, $msg);
}

function logError($msg)
{
	global $name;
	output("Error", $name, $msg);
}

function logWarn($msg)
{
	global $name;
	output("Warning", $name, $msg);
}

//TODO: fclose($fp) 释放资源，避免泄露

function output($type, $sender, $str)
{
	global $fp;
	
	$msg = "[".date("Y-m-d h:i:s",time())."][".$sender."][".$type."] ".$str;
	
	//输出到文件
	fputs($fp, $msg.PHP_EOL);
	//输出到网页
	echo $msg.PHP_EOL."<br/>";
	
	//刷新缓冲区
	ob_flush();
	flush();
}
