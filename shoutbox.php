<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
if (isset($_GET['checknew']))
{
	//file_put_contents("testshout/".$CURUSER["username"],date("H:i:s",time()));
	echo file_get_contents("shoutbox_new.html");
	die;
}
if (isset($_GET['del']))
{
	if (is_valid_id($_GET['del']))
	{
		if((get_user_class() >= $sbmanage_class))
		{
			sql_query("DELETE FROM shoutbox WHERE id=".mysql_real_escape_string($_GET['del']));
		}
	}
}
$where=$_GET["type"];
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?php echo get_font_css_uri()?>" type="text/css">
<link rel="stylesheet" href="styles/login/theme.css" type="text/css">
<link rel="stylesheet" href="styles/curtain_imageresizer.css" type="text/css">
<script src="curtain_imageresizer.js" type="text/javascript"></script><style type="text/css">body {overflow-y:scroll; overflow-x: hidden}</style>
<?php
print(get_style_addicode());
?>

<script type="text/javascript">
// //<![CDATA[
// var t;
// function countdown(time)
// {
// 	if (time <= 0){
// 	parent.document.getElementById("hbtext").disabled=false;
// 	parent.document.getElementById("hbsubmit").disabled=false;
// 	parent.document.getElementById("hbsubmit").value=parent.document.getElementById("sbword").innerHTML;
// 	}
// 	else {
// 	parent.document.getElementById("hbsubmit").value=time;
// 	time=time-1;
// 	setTimeout("countdown("+time+")", 1000); 
// 	}
// }
// function hbquota(){
// parent.document.getElementById("hbtext").disabled=true;
// parent.document.getElementById("hbsubmit").disabled=true;
// var time=10;
// countdown(time);
// //]]>
// }

//<![CDATA[
var t;
function startcountdown(time)
{
parent.document.getElementById('countdown').innerHTML=time;
time=time-1;
t=setTimeout("startcountdown("+time+")",1000);
}
function countdown(time)
{
	if (time <= 0){
	parent.document.getElementById("hbtext").disabled=false;
	parent.document.getElementById("hbsubmit").disabled=false;
	parent.document.getElementById("hbsubmit").value=parent.document.getElementById("sbword").innerHTML;
	}
	else {
	parent.document.getElementById("hbsubmit").value=time;
	time=time-1;
	setTimeout("countdown("+time+")", 1000); 
	}
}
function hbquota(){
parent.document.getElementById("hbtext").disabled=true;
parent.document.getElementById("hbsubmit").disabled=true;
var time=10;
countdown(time);
//]]>
}
function reply_to(username)
{
    if(username != "")
	parent.document.forms['shbox'].shbox_text.value = " [@" + username + "] : "+parent.document.forms['shbox'].shbox_text.value;
    parent.document.forms['shbox'].shbox_text.focus();
}

</script>
<style>
/*Shutbox Section*/
.shoutrow{min-height:42px;border-top:1px solid #aaa;border-bottom:1px solid #aaa;padding:5px;margin:-1px 10px;font-size:14px;}
.shoutrow:hover{background:#eee;}
.shoutbox_avatar{height:40px;width:40px;margin-right:5px;position:relative;float:left;}
.shoutbox_main{vertical-align:top;position:relative;margin:0 0 0 48px;font-size:14px;font-weight:normal;}
.shoutbox_main span,.shoutbox_main span a,.shoutbox_main span a b,.shoutbox_main b{vertical-align:top;}
.shoutbox_content{word-wrap:break-word;word-break: break-all;}
.shoutrow .date{position:relative;font-size:12px;color:#aaa;}
.shoutbox_reply{border-left:1px solid #aaa;}
.shoutbox_reply,.shoutbox_del,.shoutbox_user{padding:0px 5px;font-size:12px;}
.shoutbox_reply a,.shoutbox_del a{color:#aaa;}
h1{margin-left:10px;}
/*End Shutbox*/
</style>

</head>
<body class='inframe' <?php if ($_GET["type"] != "helpbox"){?> onload="<?php echo $startcountdown?>" <?php } else {?> onload="hbquota()" <?php } ?>>

<?php
if($_GET["sent"]=="yes"){
if(!$_GET["shbox_text"])
{
	$userid=0+$CURUSER["id"];
}
else
{
	$text=trim($_GET["shbox_text"]);
	if($_GET["type"]=="helpbox")
	{

		if ($showhelpbox_main != 'yes'){
			write_log("Someone is hacking shoutbox. - IP : ".getip(),'mod');
			die($lang_shoutbox['text_helpbox_disabled']);
		}
		$userid=0;
		$type='hb';
	}
	elseif ($_GET["type"] == 'shoutbox')
	{
		$userid=0+$CURUSER["id"];
		if (!$userid){
			write_log("Someone is hacking shoutbox. - IP : ".getip(),'mod');
			die($lang_shoutbox['text_no_permission_to_shoutbox']);
		}
		if ($_GET["toguest"]){
			$type ='hb';
		}else{
			if(strpos($text,"[b]游客") > 0)
			$type = 'hb';
			else
			$type = 'sb';
		}
	}
	$date=sqlesc(time());

	sql_query("INSERT INTO shoutbox (userid, date, text, type, ip) VALUES (" . sqlesc($userid) . ", $date, " . sqlesc(RemoveXSS($text)) . ", ".sqlesc($type).", ".sqlesc(getip()).")") or sqlerr(__FILE__, __LINE__);
	file_put_contents("shoutbox_new.html",mysql_insert_id());
	print "<script type=\"text/javascript\">parent.document.forms['shbox'].shbox_text.value='';</script>";
}
}

$limit = ($CURUSER['sbnum'] ? $CURUSER['sbnum'] : 70); 
if ($where == "helpbox")
{
print("<h1>求助区<a href=\"faq.php\" target=\"_blank\"><font style=\"color:green\">[FAQ]</font></a></h1>");
$sql = "SELECT * FROM shoutbox WHERE type='hb' ORDER BY date DESC LIMIT ".$limit;
}
elseif ($CURUSER['hidehb'] == 'yes' || $showhelpbox_main != 'yes'){
$sql = "SELECT * FROM shoutbox WHERE type='sb' ORDER BY date DESC LIMIT ".$limit;
}
elseif ($CURUSER){
$sql = "SELECT * FROM shoutbox ORDER BY date DESC LIMIT ".$limit;
}
else {
die("<h1>".$lang_shoutbox['std_access_denied']."</h1>"."<p>".$lang_shoutbox['std_access_denied_note']."</p></body></html>");
}
$res = sql_query($sql) or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 0)
print("\n");
else
{
?>
<script type="text/javascript" src="jquerylib/jquery-1.5.2.min.js"></script>
<script type="text/javascript">
	function retuser(value){
		//$("#shbox_text", window.parent.document).val("[To][b]"+value+"[/b]：");
		$("#shbox_text", window.parent.document).val("@[b]"+value+"[/b]:");
		$("#shbox_text", window.parent.document).focus();
		//$("#hbtext", window.parent.document).val("[To][b]"+value+"[/b]：");
		$("#hbtext", window.parent.document).val("@[b]"+value+"[/b]:");
		$("#hbtext", window.parent.document).focus();
	}
</script>
<div class="shoutbox">

<?
	print("<table border='0' cellspacing='0' cellpadding='2' width='100%' align='left'>\n");
	$i = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		if (get_user_class() >= $sbmanage_class) {
			$del="[<a href=\"shoutbox.php?del=".$arr[id]."\">".$lang_shoutbox['text_del']."</a>]";
		}
		if ($arr["userid"]) {
			$username = get_username($arr["userid"],false,true,true,true,false,false,"",true);
			$arr2 = get_user_row($arr["userid"]);
			if ($_GET["type"] != 'helpbox' && $arr["type"] == 'hb')
				$username .= $lang_shoutbox['text_to_guest'];
			}
		else{
			$school = strpos($arr["ip"],':')?school_ip_location($arr["ip"],false):'';
			$userip = str_replace(':','',$arr['ip']);
			$guestid = substr($userip,strlen($userip) - 8);
			$username = "<b title='".$school."'>游客".$guestid."</b>";
			$arr2["username"] = "游客".$guestid;
			$arr2["avatar"]=false;
		}
		if ($CURUSER['timetype'] != 'timealive')
			$time = strftime("%m.%d %H:%M",$arr["date"]);
		else $time = get_elapsed_time($arr["date"]).$lang_shoutbox['text_ago'];
		$messtext = $arr["text"];
		$messtext = str_replace($CURUSER['username'],"[color=Red]".$CURUSER['username']."[/color]",$messtext);  //将回复给自己的名字染红
// 		print("<tr><td class=\"shoutrow\"><span class='date'>[".$time."]</span> ".
// $del ." <span onclick=\"retuser('".$arr2["username"]."');\" style=\"cursor:pointer;\">[Re]</span> ". $username." " . format_comment($messtext,true,false,true,true,600,true,false)."
// </td></tr>\n");
// 		$i++;
// 	}


print("<div class=\"shoutrow\"> <img class=\"shoutbox_avatar\" src=".($arr2["avatar"] ? "'$arr2[avatar]'" : "'" . get_protocol_prefix() . $BASEURL . "/pic/default_avatar.png'")." ><h3 class=\"shoutbox_main\">  <span class=\"shoutbox_del\"> ".
$del ." </span>  ". $username." : <span class=\"shoutbox_content\"> " . format_comment($messtext,true,false,true,true,600,true,false)." </span><br> <span class='date'>[".$time."]</span> <span class=\"shoutbox_reply\" > <a onclick=\"retuser('".$arr2["username"]."');\" style=\"cursor:pointer;\" >[@]</a></span></h3></div>\n");
		$i++;
	}
		print("</table>");
	}
?>
</div>
</body>
</html>
