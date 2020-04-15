<?php
include_once $_SERVER['DOCUMENT_ROOT']."/api/lib/http.php";
include_once $_SERVER['DOCUMENT_ROOT']."/api/lib/log.php";
include_once $_SERVER['DOCUMENT_ROOT']."/api/lib/timewatch.php";
include_once $_SERVER['DOCUMENT_ROOT']."/api/lib/cookiemanager.php";

logSetName("Bilibili");
$cookie = "";

$accounts = cookieGetAll("bilibili"); //获取所有要签到的账号
//遍历，签到
foreach($accounts as $ac)
{
	$cookie = $ac->cookie;
	signin($cookie);
}



//TODO: 解决此签到只会增加硬币，不会完成每日登录经验
//TODO: 加入每日自动随机观看一个视频
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