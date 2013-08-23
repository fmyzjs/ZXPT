<?php
require_once("include/bittorrent.php");
require ("imdb/imdb.class.php");
require ("imdb/douban.php");
dbconn();
loggedinorreturn();
if (get_user_class() < $updateextinfo_class) {
permissiondenied();
}
$id = 0 + $_GET["id"];
$type = 0 + $_GET["type"];
$siteid = 0 + $_GET["siteid"]; // 1 for IMDb  2 for 豆瓣

if (!isset($id) || !$id || !is_numeric($id) || !isset($type) || !$type || !is_numeric($type) || !isset($siteid) || !$siteid || !is_numeric($siteid))
die();

$r = sql_query("SELECT * from torrents WHERE id = " . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($r) != 1)
die();

$row = mysql_fetch_assoc($r);

switch ($siteid)
{
	case 1 : 
	{
		$imdb_id = parse_imdb_id($row["url"]);
		if ($imdb_id)
		{
			$thenumbers = $imdb_id;
			$movie = new imdb ($thenumbers);
			$movieid = $thenumbers;
			$movie->setid ($movieid);
			$target = array('Title', 'Credits', 'Plot');
			($type == 2 ? $movie->purge_single(true) : "");
			set_cachetimestamp($id,"cache_stamp");
			$movie->preparecache($target,true);
			$Cache->delete_value('imdb_id_'.$thenumbers.'_movie_name');
			$Cache->delete_value('imdb_id_'.$thenumbers.'_large', true);
			$Cache->delete_value('imdb_id_'.$thenumbers.'_median', true);
			$Cache->delete_value('imdb_id_'.$thenumbers.'_minor', true);
			header("Location: " . get_protocol_prefix() . "$BASEURL/details.php?id=".htmlspecialchars($id));
		}
		break;
	}
	case 2 : 
	{
		$imdb_id = parse_imdb_id($row["url"]);
		if ($imdb_id)
		{
			$dbdata = new douban();
			$dbdata->setid($imdb_id);
			set_cachetimestamp($id,"cache_stamp");
			$Cache->delete_value('imdb_id_'.$thenumbers.'_movie_name');
			$Cache->delete_value('imdb_id_'.$thenumbers.'_large', true);
			$Cache->delete_value('imdb_id_'.$thenumbers.'_median', true);
			$Cache->delete_value('imdb_id_'.$thenumbers.'_minor', true);
			header("Location: " . get_protocol_prefix() . "$BASEURL/details.php?id=".htmlspecialchars($id));
		}
		break;
	}
	case 3 : 
	{
		$douban_id = parse_douban_id($row["dburl"]);
		if ($douban_id)
		{
			$dbdata = new douban();
			$dbdata->setid($douban_id,"douban");
			set_cachetimestamp($id,"cache_stamp");
			header("Location: " . get_protocol_prefix() . "$BASEURL/details.php?id=".htmlspecialchars($id));
		}
		break;
	}
	default :
	{
		die("Error!");
		break;
	}
}

?>
