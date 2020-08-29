<?php
include_once "../global.php";
include_once __LIB__."/http.php";
include_once __LIB__."/log.php";
include_once __LIB__."/timewatch.php";
include_once __LIB__."/cookiemanager.php";

logSetName("Bilibili");
$cookie = "";

logInfo("开始签到哔哩哔哩");
watchStart();

//获取所有要签到的账号
$accounts = new CookieManager("bilibili"); 
$accounts = $accounts->getAll();
//遍历，签到
foreach($accounts as $ac)
{
	$cookie = $ac->cookie;
	signin($cookie);
}

watchEnd();
logInfo("耗时 ".watchGetSec()." 秒。");

//TODO: 解决此签到只会增加硬币，不会完成每日登录经验
//签到
function signin($cookie)
{
	//带 Cookie 访问该 API 就可以签到
	
	$ret = hGet(array(
		"url" => "http://api.bilibili.com/x/web-interface/nav",
		"cookie" => $cookie
	));
	$json = json_decode($ret);
	
	if($json->code != 0)
	{
		logError("出现错误！code=".$json->code);
		logError("返回 json: ".$ret);
		
		return;
	}
	
	logInfo("账号 @".$json->data->uname." 签到成功");
	logInfo("硬币数量：".getCoinNum($cookie));
}


//获取硬币数量
function getCoinNum($cookie)
{
	$json = hGet(array(
		"url" => "http://account.bilibili.com/site/getCoin",
		"cookie" => $cookie
	));
	
	$json = json_decode($json);
	return $json->data->money;
}
