<?php
// basic iitonline scraper
//ini_set("display_errors", "yes");
//error_reporting(E_ALL);
set_time_limit(3600);
libxml_use_internal_errors();
include "vars.php";

foreach ($data as $class => $url) {
	$classRoot = "{$mediaRoot}{$class}/";
	if (!is_dir($classRoot)) {
		mkdir($classRoot);
	}

	// cache rss
	$classRss = "{$mediaRoot}/{$class}.rss";
	if (!file_exists($classRss) || filemtime($classRss) >= time() + $cacheTime) {
		$data = getRssData($class, $url);
		file_put_contents($classRss, trim($data));
	}

	// parse rss
	$rss = simplexml_load_file($classRss);
	// array-ify
	$rss = json_decode(json_encode($rss), true);
	//$dom = new DOMDocument();
	
	$webDir = substr($url, 0, strripos($url, "/"));
	foreach ($rss["channel"]["item"] as $el) {
		$date = $el["title"];
		$desc = $el["description"];
		$sources = $el["jwplayer_source"];
		foreach ($sources as $src) {
			$quality = $src["@attributes"]["label"];
			if ($quality !== "720p") {
				continue;
			}
			$remoteFile = "{$webDir}/" . $src["@attributes"]["file"];
			$local = "{$classRoot}{$date}-{$quality}.mp4";
			if (!file_exists($local)) {
				// use cli curl
				$userpass = sprintf("%s:%s", $username, $password);
				$exec = sprintf("curl -o %s --ntlm -u %s %s", escapeshellarg($local), escapeshellarg($userpass), escapeshellarg($remoteFile));
				note("[i] running curl to retrieve file local={$local}, remote={$remoteFile}");
				note("[d] command=`{$exec}`");
				exec($exec);
				pushbulletMessage("New lecture available", "Lecture {$date} for {$class} ({$desc}) is now available.");
			}
		}
	}
	break;
}

function pushbulletMessage($title, $body) {
	global $pushbulletHeader;
	$json = json_encode(array(
		"channel_tag" => "aldanaio-iit",
		"type" => "note",
		"title" => $title,
		"body" => $body
	));

	$c = curl_init("https://api.pushbullet.com/v2/pushes");
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_POST, true);
	curl_setopt($c, CURLOPT_HTTPHEADER, $pushbulletHeader);
	curl_setopt($c, CURLOPT_POSTFIELDS, $json);
	curl_exec($c);
	curl_close($c);
}

function getRssData($class, $url) {
	global $username, $password, $header;

	$c = curl_init($url);
	curl_setopt($c, CURLOPT_VERBOSE, false);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_USERPWD, "{$username}:{$password}");
	curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
	// below opts are optional
	curl_setopt($c, CURLOPT_HTTPHEADER, $header);
	curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.85 Safari/537.36');
	curl_setopt($c, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($c, CURLOPT_HEADER, false);
	$data = curl_exec($c);
	curl_close($c);
	note("[i] getRssData for class={$class}, url={$url}");
	return cleanRssData($data);
}

function cleanRssData($data) {
	// `normalize` tags
	return strtr($data, array(
		"jwplayer:image" => "jwplayer_image",
		"jwplayer:source" => "jwplayer_source"
	));
}

function note($text) {
	$line = sprintf("[%s]%s", date("c"), $text);
	file_put_contents("log.txt", $line, FILE_APPEND);
	echo "{$line}\n";
}
?>