<?php
if(substr($_SERVER['REQUEST_URI'],1,8)=="c2VhcmNo")
$req_uri = "/".base64_decode(substr($_SERVER['REQUEST_URI'],1));
else
$req_uri = $_SERVER['REQUEST_URI'];

/*$ips=array("173.194.32.51","74.125.225.115","74.125.128.103","74.125.128.106","74.125.128.147","173.194.127.137");
shuffle($ips);
$mirror = $ips[0];*/
$mirror='www.google.com';
if (strpos($req_uri,"q=cache")){
$host = "webcache.googleusercontent.com";
}else if(strpos($req_uri,"schhp") || strpos($req_uri,"scholar")){
$host = "scholar.google.com";
}else{
$host = $mirror;
}

$req = $_SERVER['REQUEST_METHOD'] . ' ' . $req_uri  . " HTTP/1.1\r\n";
$length = 0;
foreach ($_SERVER as $k => $v) {
	if (substr($k, 0, 5) == "HTTP_") {
		$k = str_replace('_', ' ', substr($k, 5));
		$k = str_replace(' ', '-', ucwords(strtolower($k)));
		if ($k == "Host")
			$v = $host;						# Alter "Host" header to mirrored server
		if ($k == "Accept-Encoding")
			$v = "identity;q=1.0, *;q=0";		# Alter "Accept-Encoding" header to accept unencoded content only
		if ($k == "Keep-Alive")
			continue;							# Drop "Keep-Alive" header
		if ($k == "Connection" && $v == "keep-alive")
			$v = "close";						# Alter value of "Connection" header from "keep-alive" to "close"
		$req .= $k . ": " . $v . "\r\n";
	}
}
$body = @file_get_contents('php://input');
$req .= "Content-Type: " . $_SERVER['CONTENT_TYPE'] . "\r\n";
$req .= "Content-Length: " . strlen($body) . "\r\n";
$req .= "Referer: " .  "https://www.google.com/\r\n";
$req .= "\r\n";
$req .= $body;

#print $req;

$fp = fsockopen('tls://'.$mirror, 443, $errno, $errmsg, 30);
if (!$fp) {
	print "HTTP/1.1 502 Failed to connect remote server\r\n";
	print "Content-Type: text/html\r\n\r\n";
	print "<html><body>Failed to connect to $mirror due to:<br>[$errno] $errstr</body></html>";
	exit;
}

fwrite($fp, $req);

$headers_processed = 0;
$reponse = '';
while (!feof($fp)) {
	$r = fread($fp, 8192);
	if (!$headers_processed) {
		$response .= $r;
		$nlnl = strpos($response, "\r\n\r\n");
		$add = 4;
		if (!$nlnl) {
			$nlnl = strpos($response, "\n\n");
			$add = 2;
		}
		if (!$nlnl)
			continue;
		$headers = substr($response, 0, $nlnl);
		$cookies = 'Set-Cookie: ';
		if (preg_match_all('/^(.*?)(\r?\n|$)/ims', $headers, $matches))
			for ($i = 0; $i < count($matches[0]); ++$i) {
				$ct = $matches[1][$i];
#				if (substr($ct, 0, 12) == "Set-Cookie: ") {
#					$cookies .= substr($ct, 12) . ',';
#					header($cookies);
#				} else
					header($ct, false);
#				print '>>' . $ct . "\r\n";
			}
		print substr($response, $nlnl + $add);
		$headers_processed = 1;
	} else
		print $r;
}
fclose ($fp);
if(!strpos($req_uri,"search?"))#非网页结果不添加js
die();
?>
<!--去除跳转-->
<script type="text/javascript">var h1=true,n1=true,s1=false;var ua=navigator.userAgent,wK=ua.toLowerCase().indexOf('webkit')>-1,S=location.protocol==='https:';function addEvent(a,b,c){if(a.addEventListener){a.addEventListener(b,c,false);}}
function removeEvent(a,b,c){if(a.removeEventListener){a.removeEventListener(b,c,false);}}
if(Object.defineProperty){Object.defineProperty(window,'rwt',{value:function(){},writable:false,configurable:false})}else{window.__defineGetter__('rwt',function(){return function(){}})}
if(s1){addEvent(window,'DOMNodeInserted',cache);}
function cache(){var cc=document.querySelectorAll('.vshid');if(cc){for(var i=0;i<cc.length;++i){cc[i].style.display='inline';}}}
function proxy(e){if(e&&e.localName=='a'&&(e.className=='l'||e.id=='rg_hl'||e.className=='rg_l'||e.className=='rg_ilmn'||e.parentNode.className=='vshid'||e.parentNode.className=='gl'||e.parentNode.className=='r')){e.onmousedown?e.removeAttribute('onmousedown'):0;var m=/(&url=([^&]+)|imgurl=([^&]+))(&w=\d+&h=\d+)?&ei/g.exec(decodeURIComponent(e.href));if(m)e.href=m[2]||m[3];if(n1)e.target="_blank";if(h1){if(wK&&!S){e.rel="noreferrer";}else if(!S&&e.href.indexOf('http-equiv="refresh"')==-1){e.href='data:text/html, <meta http-equiv="refresh" content="0;URL='+encodeURIComponent(e.href)+'" charset="utf-8">';}}}}
function tunnel(e,f){if(e&&e.localName=='a'&&(e.className=='l'||e.id=='rg_hl'||e.className=='rg_ilmn'||e.className=='irc_but'||e.className=='rg_l'||e.parentNode.className=='vshid'||e.parentNode.className=='gl'||e.parentNode.className=='r')){if(e.href.indexOf('http-equiv="refresh"')>-1){var rLink=/URL=([^"]+)/g.exec(decodeURIComponent(e.href));if(rLink){e.href=rLink[1];}}}
removeEvent(f,'mouseout',fixer);}
function fixer(e){var a=e.target,b=a;if(a.localName!='a'){for(;a;a=a.parentNode){tunnel(a,b);}}else{tunnel(a,b);}}
function doStuff(e){var a=e.target;addEvent(a,'mouseout',fixer);if(a&&a.className=='rg_i'){a.removeAttribute('class');}
if(a.localName!='a'){for(;a;a=a.parentNode){proxy(a);}}else{proxy(a);}}
addEvent(window,"mousedown",doStuff);
</script>
<!--修改快照-->
<script src="http://git.oschina.net/loonhxl/jbase64/raw/master/jbase64.js?raw_enc=us-ascii"></script>
<script type="text/javascript">var objs=document.getElementsByClassName('fl');for(var i=0;i<objs.length;i++){if(objs[i].href.indexOf("http://webcache.googleusercontent.com/")!=-1){objs[i].href="/"+BASE64.encoder(objs[i].href.substring(38));}}//modify existing "cached" button
var _mcite=document.getElementsByClassName('kv');if(_mcite){for(var i=0;i<_mcite.length;i++)_mcite[i].innerHTML+="&nbsp;&nbsp;<a target='_blank' href=/"+BASE64.encoder("search?newwindow=1&output=search&sclient=psy-ab&psj=1&q=cache%3A"+encodeURIComponent(_mcite[i].innerText)+"&oq=cache%3A"+encodeURIComponent(_mcite[i].innerText))+" style='color:#999'>快照</a>";}//add
</script>
<!--统计233-->
<script type="text/javascript" src="http://gostats.cn/js/counter.js"></script>
<script type="text/javascript">_gos='monster.gostats.cn';_goa=467540;_got=5;_goi=1;_gol='计数器制作';_GoStatsRun();</script>
<script type="text/javascript">//replace to yooooo.us
var btns=document.getElementsByClassName("gbzt");for(var i in btns){if (btns[i].href){btns[i].href=btns[i].href.replace("http://www.google.com/","/").replace(/webhp\?[^"]+/,"");}}var logo=document.getElementById("logo");logo.href=logo.href.replace("http://www.google.com/","").replace(/webhp\?[^"]+/,"");</script>
