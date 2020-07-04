<?php
define("COOKIE_PATH", __ROOT__."/api/stats/cookies.json");

//检查文件是否存在
function checkFileExists()
{
	if(!file_exists(COOKIE_PATH))
	{
		$fp = fopen(COOKIE_PATH, "w");	
		fclose($fp);
	}
}

function read()
{
	checkFileExists();
	$obj = json_decode(file_get_contents(COOKIE_PATH));
	return is_object($obj) ? $obj : array();
}

function write($obj)
{
	checkFileExists();
	file_put_contents(COOKIE_PATH, json_encode($obj, JSON_UNESCAPED_UNICODE));
}


//获取指定网站的所有账号的 Cookie
function cookieGetAll($sitename)
{
	$obj = read();
	return isset($obj->$sitename) ? $obj->$sitename : "";
}

function cookieGet($sitename, $index)
{
	$obj = cookieGetAll($sitename);
	if(!is_array($obj))
		return "";
	else if(count($obj) <= 0)
		return "";
	else
		return $obj[$index];
}

//添加指定网站的账号
function cookieAdd($sitename, $name, $cookie)
{
	$obj = read();
	$index = array_push($obj->$sitename, array(
		"name" => $name,
		"cookie" => $cookie
		));
	
	write($obj);
}

//TODO: 完成删除功能
function cookieDelete($sitename, $index)
{
	$obj = read();
	//unset($obj->$sitename[$index]);
	//unset($obj->tieba[0]);
	write($obj);
}