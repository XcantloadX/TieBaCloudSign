<?php
include_once "../global.php";
include_once __LIB__."/http.php";
include_once __LIB__."/log.php";
include_once __LIB__."/timewatch.php";
include_once __LIB__."/cookiemanager.php";

set_time_limit(0); //设置脚本执行时间无上限
date_default_timezone_set("Asia/Shanghai"); //设置时区

logSetName("TieBa");
logInfo("开始签到百度贴吧");

//TODO: 增加多个账号的检测
//读取、检查 Cookie
$account = cookieGet("tieba", 0);
if($account == "" || $account->cookie == "")
{
	logError("Cookie 未设置！无法签到！");
	exit;
}
else
	$cookie = $account->cookie;

signAll();


/* 签到单个贴吧
@param 该贴吧名称
*/
function sign($name)
{
	global $cookie;
	$data = array("ie" => "utf-8", "kw" => $name);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://tieba.baidu.com/sign/add");
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36"); //设置 UA
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true); // 发送 Post 请求
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //请求参数
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回内容储存到变量中
	
	$json = curl_exec($ch);
	
	return $json;
}


//签到所有贴吧
function signAll()
{
	global $cookie;
	logInfo("开始签到账号 @".getUserName());
	
	$bars = getAllBars();
	$signed = 0; //签到成功个数
	watchStart();
	
	//循环签到所有贴吧
	foreach($bars as $name=>$level)
	{
		
		$json = sign($name);
		$json = json_decode($json);
		
		//错误码
		$code = intval($json->no);
		
		if($code == 1101)
		{
			logWarn("你已经签到过 ".$name."吧 了！当前为 ".$level." 级");
		}
		else if($code == 1990055)
		{
			logError("Cookie 已失效，请重新设置！");
			logError("返回 json：".json_encode($json));
			break;
		}
		else if($code != 0)
		{
			logError("签到 ".$name."吧 时发生错误！");
			logError("返回 json：".json_encode($json));
		}
		else
		{
			logInfo("签到 ".$name."吧 成功。当前为 ".$level." 级");
			$signed++;
		}
	}
	
	watchEnd();
	logInfo("已成功签到：".$signed."/".count($bars)." 个贴吧。");
	logInfo("耗时 ".watchGetSec()." 秒。");
}

//获取所有关注的贴吧的名称和等级
function getAllBars()
{
	global $cookie;
	
	//获取贴吧首页
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://tieba.baidu.com");
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.96 Safari/537.36"); //设置 UA
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$html = curl_exec($ch);
	
	$start = strpos($html, "spage/widget/forumDirectory") + 27 + 2;
	$end = strpos($html, "</script>", $start) - 2;
	$json = substr($html, $start, $end - $start);

	//解析 json
	$json = json_decode($json);
	$bars = array();
	
	//遍历出所有名称和等级
	for($i = 0; $i < count($json->forums); $i++)
	{
		$bars[$json->forums[$i]->forum_name] = $json->forums[$i]->level_id;
		//array_push($names, $json->forums[$i]->forum_name);
	}
	
	return $bars;
}

//获取用户名称
function getUserName()
{
	global $cookie;
	
	$html = hGet(array(
		"url" => "https://tieba.baidu.com",
		"cookie" => $cookie
	));
	
	//截取用户名
	$start = strpos($html, '"user_name"') + strlen('"user_name"') + 3;
	$end = strpos($html, '"', $start);
	$name = substr($html, $start, $end - $start);
	
	return $name;
}

?>
