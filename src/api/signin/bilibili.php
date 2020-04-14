<?php
include_once $_SERVER['DOCUMENT_ROOT']."/api/lib/http.php";
include_once $_SERVER['DOCUMENT_ROOT']."/api/lib/log.php";
include_once $_SERVER['DOCUMENT_ROOT']."/api/lib/timewatch.php";

$cookie = "";

signin($cookie);
logSetName("Bilibili");
logInfo("完成");

//签到
function signin($cookie)
{
	//带 Cookie 访问B站首页就可以签到
	
	hGet(array(
		"url" => "http://bilibili.com",
		"cookie" => $cookie
	));
}