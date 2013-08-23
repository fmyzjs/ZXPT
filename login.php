<?php
require_once("include/bittorrent.php");
dbconn();

$langid = 0 + $_GET['sitelanguage'];
if ($langid)
{
	$lang_folder = validlang($langid);
	if(get_langfolder_cookie() != $lang_folder)
	{
		set_langfolder_cookie($lang_folder);
		header("Location: " . $_SERVER['PHP_SELF']);
	}
}
require_once(get_langfile_path("", false, $CURLANGDIR));

failedloginscheck ();
cur_user_check () ;
stdhead($lang_login['head_login']);

$s = "<select name=\"sitelanguage\" onchange='submit()'>\n";

$langs = langlist("site_lang");

foreach ($langs as $row)
{
	if ($row["site_lang_folder"] == get_langfolder_cookie()) $se = "selected=\"selected\""; else $se = "";
	$s .= "<option value=\"". $row["id"] ."\" ". $se. ">" . htmlspecialchars($row["lang_name"]) . "</option>\n";
}
$s .= "\n</select>";
?>

<p><?php echo $lang_login['text_QQ']?></p>

<?php

unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		print("<h1>" . $lang_login['h1_not_logged_in']. "</h1>\n");
		print("<p><b>" . $lang_login['p_error']. "</b> " . $lang_login['p_after_logged_in']. "</p>\n");
	}
}

?>


<table class="main" width="100%">

<?php
if ($showhelpbox_main != 'no'){?>

<td width="467" height="500"  align="center" style="border:0;padding-left:10px"  >
<!-- <h2><?php echo $lang_login['text_helpbox'] ?><font class="small"></font></h2> -->

<?php
print("<table width='467'  height='480' valign='top' border='1' cellspacing='0' cellpadding='1'><tr><td class=\"text\">\n");
	if ($Advertisement->enable_ad()){
		$shout_ad = $Advertisement->get_ad('shoutlogin');
		print("<div id=\"ad_shoutindex\">".$shout_ad[0]."</div>");
	}
?>

<script type="text/javascript">
	var shoutbox_value = 0;
	setInterval(check_shoutbox_new,2000);
        function check_shoutbox_new()
        {
                $.get("shoutbox_new.html",function(result){
			var value = parseInt(result);
			if((shoutbox_value < value && shoutbox_value > 0) || value == 0){
				$("[name=sbox]").attr("src",$("[name=sbox]").attr("src"));
			}
			shoutbox_value = value;
		});
        }
</script>
<?
print("<iframe src='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php?type=helpbox' width='480'  height='350' valign='top' frameborder='0' name='sbox' marginwidth='0' marginheight='0'></iframe><br /><br />\n");
print("<form action='" . get_protocol_prefix() . $BASEURL . "/shoutbox.php' id='helpbox' width='480' method='get' target='sbox' name='shbox'>");
print($lang_login['text_message']."<input type='text' id=\"hbtext\" name='shbox_text' autocomplete='off' style='width: 410px; border: 1px solid gray' ><input type='submit' id='hbsubmit' class='btn' name='shout' value=\"".$lang_login['sumbit_shout']."\" /><input type='reset' class='btn' value=".$lang_login['submit_clear']." /> <input type='hidden' name='sent' value='yes'><input type='hidden' name='type' value='helpbox' />\n");
print("<div id=sbword style=\"display: none\">".$lang_login['sumbit_shout']."</div>");
print(smile_row("shbox","shbox_text"));
print("</td></tr></table></form></td>");

}
?>

<td  height="500" valign="top" align="center" >
<!-- <h2><?php echo "登陆" ?><font class="small"> </font></h2> -->
<!--login-->

<form method="post" action="takelogin.php">
<div class="login_form" border='0' >
<div class="login_logo"></div>
<p><input autocomplete="off" class="text" id="icon_usr" type="text" name="username" placeholder="用户名或注册邮箱"/></p>
<p><input autocomplete="off" class="text" id="icon_pwd" type="password" name="password" placeholder="密码"/></p>
<?php show_image_code()?>
<?php

if ($securelogin == "yes") 
	$sec = "checked=\"checked\" disabled=\"disabled\"";
elseif ($securelogin == "no")
	$sec = "disabled=\"disabled\"";
elseif ($securelogin == "op")
	$sec = "";

if ($securetracker == "yes") 
	$sectra = "checked=\"checked\" disabled=\"disabled\"";
elseif ($securetracker == "no")
	$sectra = "disabled=\"disabled\"";
elseif ($securetracker == "op")
	$sectra = "";
?>
<div class="advance_form">
<p><input class="checkbox" type="checkbox" name="logout" value="yes" /><?php echo $lang_login['checkbox_auto_logout']?></p>
<p><input class="checkbox" type="checkbox" name="securelogin" value="yes" /><?php echo $lang_login['checkbox_restrict_ip']?></p>
<p><input class="checkbox" type="checkbox" name="ssl" value="yes" <?php echo $sec?> /><?php echo $lang_login['checkbox_ssl']?><br /></p>
<p><input class="checkbox" type="checkbox" name="trackerssl" value="yes" <?php echo $sectra?> /><?php echo $lang_login['checkbox_ssl_tracker']?></p>
</div>

<p style="height:40px;margin-top:20px;"><span class="links"><a href="signup.php?type=cardreg">校内注册</a> | <a href="recover.php">找回密码</a> | <a href="confirm_resend.php">重发验证邮件</a></span><input type="submit" class="submit" value="登录" class="btn" /></p>
<?php
if ($smtptype != 'none'){
?>




<?php

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");

}
?>
</div>
</form>
<!--login-->
</td>
</td>
</td>
</table>
</table>

</div>
<?php
stdfoot();
