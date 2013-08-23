<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();

$id = 0 + $_GET['id'];
if (!$id)
	die();

$res = sql_query("SELECT torrents.*, categories.mode as cat_mode FROM torrents LEFT JOIN categories ON category = categories.id WHERE torrents.id = $id");
$row = mysql_fetch_array($res);
if (!$row) die();

if ($enablespecial == 'yes' && get_user_class() >= $movetorrent_class)
	$allowmove = true; //enable moving torrent to other section
else $allowmove = false;

$sectionmode = $row['cat_mode'];
if ($sectionmode == $browsecatmode)
{
	$othermode = $specialcatmode;
	$movenote = $lang_edit['text_move_to_special'];
}
else
{
	$othermode = $browsecatmode;
	$movenote = $lang_edit['text_move_to_browse'];
}

$showsource = (get_searchbox_value($sectionmode, 'showsource') || ($allowmove && get_searchbox_value($othermode, 'showsource'))); //whether show sources or not
$showmedium = (get_searchbox_value($sectionmode, 'showmedium') || ($allowmove && get_searchbox_value($othermode, 'showmedium'))); //whether show media or not
$showcodec = (get_searchbox_value($sectionmode, 'showcodec') || ($allowmove && get_searchbox_value($othermode, 'showcodec'))); //whether show codecs or not
$showstandard = (get_searchbox_value($sectionmode, 'showstandard') || ($allowmove && get_searchbox_value($othermode, 'showstandard'))); //whether show standards or not
$showprocessing = (get_searchbox_value($sectionmode, 'showprocessing') || ($allowmove && get_searchbox_value($othermode, 'showprocessing'))); //whether show processings or not
$showteam = (get_searchbox_value($sectionmode, 'showteam') || ($allowmove && get_searchbox_value($othermode, 'showteam'))); //whether show teams or not
$showaudiocodec = (get_searchbox_value($sectionmode, 'showaudiocodec') || ($allowmove && get_searchbox_value($othermode, 'showaudiocodec'))); //whether show audio codecs or not

stdhead($lang_edit['head_edit_torrent'] . "\"". $row["name"] . "\"");
if (!isset($CURUSER) || ($CURUSER["id"] != $row["owner"] && get_user_class() < $torrentmanage_class)) {
	print("<h1 align=\"center\">".$lang_edit['text_cannot_edit_torrent']."</h1>");
	print("<p>".$lang_edit['text_cannot_edit_torrent_note']."</p>");
}
else {
	print("<form method=\"post\" id=\"compose\" name=\"edittorrent\" action=\"takeedit.php\" enctype=\"multipart/form-data\">");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\" />");
	if (isset($_GET["returnto"]))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />");
	print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" width=\"940\">\n");


	$count_dispname=mb_strlen($row["name"],"UTF-8");
	if (!$displaysmalldescr || $row["small_descr"] == "")// maximum length of torrent name
		$max_length_of_torrent_name = 45;
	elseif ($CURUSER['fontsize'] == 'large')
		$max_length_of_torrent_name = 40;
	elseif ($CURUSER['fontsize'] == 'small')
		$max_length_of_torrent_name = 40;
	else $max_length_of_torrent_name = 40;

	if($count_dispname > $max_length_of_torrent_name)
		$cname=mb_substr($row["name"], 0, $max_length_of_torrent_name-2,"UTF-8") . "..";


	print("<tr><td class='colhead' colspan='2' align='center'>".htmlspecialchars($cname)."</td></tr>");
	tr($lang_edit['row_torrent_name']."<font color=\"red\">*</font>", "<input type=\"text\" style=\"width: 650px;\" name=\"name\" value=\"" . htmlspecialchars($row["name"]) . "\" />", 1);
	if ($smalldescription_main == 'yes')
		tr($lang_edit['row_small_description'], "<input type=\"text\" style=\"width: 650px;\" name=\"small_descr\" value=\"" . htmlspecialchars($row["small_descr"]) . "\" />", 1);

	get_external_tr($row["url"]);
	get_dbexternal_tr($row["dburl"]);

	if ($enablenfo_main=='yes')
		tr($lang_edit['row_nfo_file'], "<font class=\"medium\"><input type=\"radio\" name=\"nfoaction\" value=\"keep\" checked=\"checked\" />".$lang_edit['radio_keep_current'].
	"<input type=\"radio\" name=\"nfoaction\" value=\"remove\" />".$lang_edit['radio_remove'].
	"<input id=\"nfoupdate\" type=\"radio\" name=\"nfoaction\" value=\"update\" />".$lang_edit['radio_update']."</font><br /><input type=\"file\" name=\"nfo\" onchange=\"document.getElementById('nfoupdate').checked=true\" />", 1);
	print("<tr><td class=\"rowhead\">".$lang_edit['row_description']."<font color=\"red\">*</font></td><td class=\"rowfollow\">");
	textbbcode("edittorrent","descr",($row["descr"]), false);
	print("</td></tr>");
?>
<?php
	$s = "<select name=\"type\" id=\"oricat\" onchange=\"secondtype(this);\">";

	$cats = genrelist($sectionmode);
	foreach ($cats as $subrow) {
		$s .= "<option value=\"" . $subrow["id"] . "\"";
		if ($subrow["id"] == $row["category"])
		$s .= " selected=\"selected\"";
		$s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
	}

	$s .= "</select>\n";
	if ($allowmove){
		$s2 = "<select name=\"type\" id=newcat disabled>\n";
		$cats2 = genrelist($othermode);
		foreach ($cats2 as $subrow) {
			$s2 .= "<option value=\"" . $subrow["id"] . "\"";
			if ($subrow["id"] == $row["category"])
			$s2 .= " selected=\"selected\"";
			$s2 .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
		}
		$s2 .= "</select>\n";
		$movecheckbox = "<input type=\"checkbox\" id=movecheck name=\"movecheck\" value=\"1\" onclick=\"disableother2('oricat','newcat')\" />";
	}
	tr($lang_edit['row_type']."<font color=\"red\">*</font>", $s.($allowmove ? "&nbsp;&nbsp;".$movecheckbox.$movenote.$s2 : ""), 1);
	if ($showsource || $showmedium || $showcodec || $showaudiocodec || $showstandard || $showprocessing){
		if ($showsource){
			$source_select = torrent_selection($lang_edit['text_source'],"source_sel","sources",$row["source"]);
		}
		else $source_select = "";

		if ($showmedium){
			$medium_select = torrent_selection($lang_edit['text_medium'],"medium_sel","media",$row["medium"]);
		}
		else $medium_select = "";

		if ($showcodec){
			$codec_select = torrent_selection($lang_edit['text_codec'],"codec_sel","codecs",$row["codec"]);
		}
		else $codec_select = "";

		if ($showaudiocodec){
			$audiocodec_select = torrent_selection($lang_edit['text_audio_codec'],"audiocodec_sel","audiocodecs",$row["audiocodec"]);
		}
		else $audiocodec_select = "";

		if ($showstandard){
			$standard_select = torrent_selection($lang_edit['text_standard'],"standard_sel","standards",$row["standard"]);
		}
		else $standard_select = "";

		if ($showprocessing){
			$processing_select = torrent_selection($lang_edit['text_processing'],"processing_sel","processings",$row["processing"]);
		}
		else $processing_select = "";

		tr($lang_edit['row_quality']."<font color=red>*</font>", $source_select . $medium_select . $codec_select . $audiocodec_select. $standard_select . $processing_select, 1);
	}

	if ($showteam){
		if ($showteam){
			$team_select = torrent_selection($lang_edit['text_team'],"team_sel","teams",$row["team"]);
		}
		else $showteam = "";

		tr($lang_edit['row_content'],$team_select,1);
	}
	tr($lang_edit['row_check'], "<input type=\"checkbox\" name=\"visible\"" . ($row["visible"] == "yes" ? " checked=\"checked\"" : "" ) . " value=\"1\" /> ".$lang_edit['checkbox_visible']."&nbsp;&nbsp;&nbsp;".(get_user_class() >= $beanonymous_class || get_user_class() >= $torrentmanage_class ? "<input type=\"checkbox\" name=\"anonymous\"" . ($row["anonymous"] == "yes" ? " checked=\"checked\"" : "" ) . " value=\"1\" />".$lang_edit['checkbox_anonymous_note']."&nbsp;&nbsp;&nbsp;" : "").(get_user_class() >= $torrentmanage_class ? "<input type=\"checkbox\" name=\"banned\"" . (($row["banned"] == "yes") ? " checked=\"checked\"" : "" ) . " value=\"yes\" /> ".$lang_edit['checkbox_banned'] : ""), 1);
	if (get_user_class()>= $torrentsticky_class || (get_user_class() >= $torrentmanage_class && $CURUSER["class"] >= '13')){
		$pickcontent = "";
	
		if(get_user_class()>=$torrentsticky_class)
		{
			$pickcontent .= "<b>".$lang_edit['row_special_torrent'].":&nbsp;</b>"."<select name=\"sel_spstate\" style=\"width: 100px;\">" .promotion_selection($row["sp_state"], 0). "</select>&nbsp;&nbsp;&nbsp;<b>".$lang_edit['row_special_torrent_time'].":&nbsp;</b><input type=\"text\" name=\"freetime\" size=3 maxlength=3 /><b>天(留空不修改，-1为永久)<input type=\"text\" name=\"freetimeh\" size=2 maxlength=2 />小时，当前至$row[endfree]</b><br />";
			$pickcontent .= "<b>".$lang_edit['row_torrent_position'].":&nbsp;</b>"."<select name=\"sel_posstate\" style=\"width: 100px;\">" .
			"<option" . (($row["pos_state"] == "normal") ? " selected=\"selected\"" : "" ) . " value=\"0\">".$lang_edit['select_normal']."</option>" .
			"<option" . (($row["pos_state"] == "sticky") ? " selected=\"selected\"" : "" ) . " value=\"1\">".$lang_edit['select_sticky']."</option>" .
			"</select>&nbsp;&nbsp;&nbsp;<b>".$lang_edit['row_torrent_position_time'].":&nbsp;</b><input type=\"text\" name=\"stickytime\" size=3 maxlength=3 /><b>天(留空不修改，-1为永久)<input type=\"text\" name=\"stickytimeh\" size=2 maxlength=2 />小时，当前至$row[endsticky]</b><br />";
		}
		if(get_user_class()>=$torrentmanage_class)
		{
			$pickcontent .= "<b>".$lang_edit['row_recommended_movie'].":&nbsp;</b>"."<select name=\"sel_recmovie\" style=\"width: 100px;\">" .
			"<option" . (($row["picktype"] == "normal") ? " selected=\"selected\"" : "" ) . " value=\"0\">".$lang_edit['select_normal']."</option>" .
			"<option" . (($row["picktype"] == "hot") ? " selected=\"selected\"" : "" ) . " value=\"1\">".$lang_edit['select_hot']."</option>" .
			"<option" . (($row["picktype"] == "classic") ? " selected=\"selected\"" : "" ) . " value=\"2\">".$lang_edit['select_classic']."</option>" .
			"<option" . (($row["picktype"] == "recommended") ? " selected=\"selected\"" : "" ) . " value=\"3\">".$lang_edit['select_recommended']."</option>" .
			"</select>";
		}
		tr($lang_edit['row_pick'], $pickcontent, 1);
	}

	print("<tr><td class=\"toolbox\" colspan=\"2\" align=\"center\"><input id=\"qr\" type=\"submit\" value=\"".$lang_edit['submit_edit_it']."\" /> <input type=\"reset\" value=\"".$lang_edit['submit_revert_changes']."\" /></td></tr>\n");
	print("</table>\n");
	print("</form>\n");
	print("<br /><br />");
	print("<form method=\"post\" action=\"delete.php\">\n");
	print("<input type=\"hidden\" name=\"id\" value=\"$id\" />\n");
	if (isset($_GET["returnto"]))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($_GET["returnto"]) . "\" />\n");
	print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n");
	print("<tr><td class=\"colhead\" align=\"left\" style='padding-bottom: 3px' colspan=\"2\">".$lang_edit['text_delete_torrent']."</td></tr>");
	tr("<input name=\"reasontype\" type=\"radio\" value=\"1\" />&nbsp;".$lang_edit['radio_dead'], $lang_edit['text_dead_note'], 1);
	tr("<input name=\"reasontype\" type=\"radio\" value=\"2\" />&nbsp;".$lang_edit['radio_dupe'], "<input type=\"text\" style=\"width: 200px\" name=\"reason[]\" />", 1);
	tr("<input name=\"reasontype\" type=\"radio\" value=\"3\" />&nbsp;".$lang_edit['radio_nuked'], "<input type=\"text\" style=\"width: 200px\" name=\"reason[]\" />", 1);
	tr("<input name=\"reasontype\" type=\"radio\" value=\"4\" />&nbsp;".$lang_edit['radio_rules'], "<input type=\"text\" style=\"width: 200px\" name=\"reason[]\" />".$lang_edit['text_req'], 1);
	tr("<input name=\"reasontype\" type=\"radio\" value=\"5\" checked=\"checked\" />&nbsp;".$lang_edit['radio_other'], "<input type=\"text\" style=\"width: 200px\" name=\"reason[]\" />".$lang_edit['text_req'], 1);
	print("<tr><td class=\"toolbox\" colspan=\"2\" align=\"center\"><input type=\"submit\" style='height: 25px' value=\"".$lang_edit['submit_delete_it']."\" /></td></tr>\n");
	print("</table>");
?>

<script type="text/javascript">
$(document).ready(function(){
	var oricat = $("#source_sel").val();
	secondtype(document.getElementById("oricat"));
	$("#source_sel").val(oricat);
});
function uplist(name,list) {
        var childRet = document.getElementById(name);
        for (var i = childRet.childNodes.length-1; i >= 0; i--) { 
                childRet.removeChild(childRet.childNodes.item(i)); 
        } 
        for (var j=0; j<list.length; j++) {
                var ret = document.createDocumentFragment();
                var newop = document.createElement("option");
                newop.id = list[j][0];
                newop.value = list[j][0]; 
                newop.appendChild(document.createTextNode(list[j][1])); 
                ret.appendChild(newop); 
                document.getElementById(name).appendChild(ret);
}
}

function secondtype(value) {
<?
        $cats = genrelist($browsecatmode);
        foreach ($cats as $row){
        $catsid = $row['id'];
        $secondtype = searchbox_item_list("sources",$catsid);
        $secondsize = count($secondtype,0);
        print("var lid".$catsid." = new Array(");
        for($i=0; $i<$secondsize; $i++){
                print("['".$secondtype[$i]['id']."','".$secondtype[$i]['name']."']");
                if($i<$secondsize-1) print(",");
        }
        print(");\n");
        }
?>
<?
        $cats = genrelist($browsecatmode);
        print("switch(value.value){\n");
        foreach ($cats as $row){
$catsid = $row['id'];
        print("\tcase \"".$catsid."\": ");
        print("uplist(\"source_sel\",lid".$catsid.");");
        print("break;\n");
        }
        print("}\n");
?>
}
</script>

<?php
	print("</form>\n");
}
stdfoot();
