<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
function bark($msg)
{
   stdhead();
   stdmsg($lang_takeflush['std_failed'], $msg);
   stdfoot();
   exit;
}

$id = 0 + $_GET['id'];
int_check($id,true);

if (get_user_class() >= UC_MODERATOR || $CURUSER[id] == "$id")
{  
   $deadtime = deadtime();

//$deadtime2 = deadtime();
//$deadtime = time()-600
 //sql_query("DELETE FROM peers WHERE ((last_action< FROM_UNIXTIME($deadtime) AND seeder = 'no)' or (last_action<FROM_UNIXTIME($deadtime2) AND seeder = 'yes')) and userid = " . sqlesc($id));

	sql_query("DELETE FROM peers WHERE last_action < FROM_UNIXTIME($deadtime) AND userid=" . sqlesc($id));
   $effected = mysql_affected_rows();

   stderr($lang_takeflush['std_success'], "$effected ".$lang_takeflush['std_ghost_torrents_cleaned']);
}
else
{
   bark($lang_takeflush['std_cannot_flush_others']);
}
