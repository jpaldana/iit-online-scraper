<?php
$mediaRoot = "media/";
$username = "@@USERNAME HERE@@";
$password = "@@PASSWORD HERE@@";
$header = array(
	"Host: media.iitonline.iit.edu",
	"Connection: keep-alive",
	"Pragma: no-cache",
	"Cache-Control: no-cache",
	"Upgrade-Insecure-Requests: 1",
	"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
	"Accept-Language: en-US,en;q=0.9"
);
$cacheTime = 3600; // 1 hour?
$pushbulletHeader = array(
	"Access-Token: @@ACCESSTOKEN HERE@@",
	"Content-Type: application/json",
);
$data = array(
	"classname" => "rss feed"
);
?>