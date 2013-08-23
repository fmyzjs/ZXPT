<?php 
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if(!isset($_GET['id'])|| !isset($_GET['u'])){
		exit;
		}
		$id = $_GET['id'];
		$u = $_GET['u'];
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>v6-在线点播</title>
</head>
<body style="text-align:center;background: #87CEEB;">
<p><b>没有安装最新v6speed?前往 <a href="http://www.v6speed.org/v6Speed/">官网!</a></b></p>
<p><b>或者点击<a href="v6Speed_setup.exe">这里</a>下载最新安装包</b></p>
<p><b><font color="red">首次使用请注意：1.设置下载目录为较大硬盘目录<br />2.需打开v6speed客户端并且同意v6播放器绑定影音播放文件<br/>3.右键可下载字幕，支持拖曳，请下载安装最新版本...即将跳转</font></b><b></b></p>
<p><a href="http://www.v6speed.org/v6Speed/v6Speed_setup.exe"><img src="pic/v6playerad.png" align="center"/></a></p>
<?php 

$domain=$_SERVER['HTTP_HOST'];
// 将 www.yy.org 替换为你自己站点 地址
header("Refresh: 3; url=v6player://$id&ty=1&ro=2&id=$id&ua=$u&url=$domain");?>
</body>
</html>