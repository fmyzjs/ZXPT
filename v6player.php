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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Refresh" content="<?php echo $refresh?>; url=<?php echo get_protocol_prefix() . $BASEURL?>/shoutbox.php?type=<?php echo $where?>">
<link rel="stylesheet" href="<?php echo get_font_css_uri()?>" type="text/css">
<link rel="stylesheet" href="<?php echo get_css_uri()."theme.css"?>" type="text/css">
<link rel="stylesheet" href="styles/curtain_imageresizer.css" type="text/css">
<script src="curtain_imageresizer.js" type="text/javascript"></script>
<style type="text/css">body {overflow-y:scroll; overflow-x: hidden}</style>
<title>v6-在线点播</title>
</head>
<body >
<div align="center">
<p><b><font color="white" size="5pt">没有安装最新v6speed?前往 <a href="http://www.v6speed.org/v6Speed/">官网!</a></font></b></p>
<p><b><font color="white" size="5pt">或者点击<a href="v6Speed_setup.exe">这里</a>下载最新安装包</font></b></p>

<p><a href="http://www.v6speed.org/v6Speed/v6Speed_setup.exe"><img src="pic/v6playerad.png" align="center"/></a></p>
<?php 

$domain=$_SERVER['HTTP_HOST'];
// 将 www.yy.org 替换为你自己站点 地址
header("Refresh: 3; url=v6player://$id&ty=1&ro=2&id=$id&ua=$u&url=$domain");?>
</div>
</body>
</html>