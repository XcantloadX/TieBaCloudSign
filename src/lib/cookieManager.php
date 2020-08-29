<?php
require_once "../global.php";
define("COOKIE_PATH", __USER__."/cookies.json");

class CookieManager
{
	var $siteName = "";
	var $jsonObj;
	var $siteObj;
	
	function __construct($siteName)
	{
		if(!file_exists(COOKIE_PATH))
		{
			$fp = fopen(COOKIE_PATH, "w");	
			fwrite($fp, '{"'.$siteName.'": []}');
			fclose($fp);
		}
		$this->siteName = $siteName;
		$this->jsonObj = json_decode(file_get_contents(COOKIE_PATH));
		$this->siteObj = $this->jsonObj->$siteName;
	}
	
	//列出所有账号
	function getAll()
	{
		return isset($this->siteObj) ? $this->siteObj : array();
	}
	
	/*
	* @param int $index 下标
	*/
	function get($index)
	{
		$array = $this->getAll();
		return $array[$index];
	}
	
	function add($accountName, $cookie)
	{
		$index = array_push($this->siteObj, array(
			"name" => $accountName,
			"cookie" => $cookie
		));
		$this->save();
		return $index;
	}
	
	function remove($index)
	{
		unset($this->siteObj[$index]);
		$this->save();
	}
	
	private function save()
	{
		$siteName = $this->siteName;
		$this->jsonObj->$siteName = $this->siteObj;
		file_put_contents(COOKIE_PATH, json_encode($this->jsonObj, JSON_UNESCAPED_UNICODE));
	}
}