<?

class douban {
	var $doubanxml,$dbarray;
	var $dbinfo;
	var $cachepath = "",$siteurl = "",$apikey = "";
	function __construct() {
		$this->cachepath = "./imdb/cache/";
		$this->imagepath = "./imdb/images/";
		$this->apikey = "0022e42002ad5ae10068ac60080810a3";
   	}

	function __destruct() {
		xml_parser_free($xmlparser);
   	}
	function prinfo(){
		$page = "";
		$page .= "资源名称：";
		$page .= $this->dbinfo['title'];
		$page .="（";
		foreach($this->dbinfo['aka'] as $key => $value){
			if($value == $this->dbinfo['aka'][0] && $key !=0)
				;
			else
				if($key > 0)
					$page .="，".$value;
				else
					$page .= $value;
		}
		$page .="）";
		$page .="<br />";
		$page .="主要演员：";
		foreach($this->dbinfo['author'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="其他演员：";
		foreach($this->dbinfo['cast'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="电影类型：";
		foreach($this->dbinfo['movie_type'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="电影语言：";
		foreach($this->dbinfo['language'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="产　　地：";
		$page .=$this->dbinfo[country][0];
		$page .="<br />";
		$page .="导　　演：";
		$page .=$this->dbinfo[director][0];
		$page .="<br />";
		$page .="发布时间：";
		$page .=$this->dbinfo[pubdate][0];
		$page .="<br />";
		$page .="电影时长：";
		$page .=$this->dbinfo[movie_duration][0];
		$page .="<br />";
		$page .="豆瓣标签：";
		foreach($this->dbinfo['tag'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .=$value;
		}
		$page .="<br />";
		$page .="豆瓣评分：";
		$page .=$this->dbinfo[rating];
		$page .="<br />";
		$page .= "豆瓣链接：";
		$page .= "<a href=\"".$this->dbinfo[link][alternate]."\" target=\"_blank\">".$this->dbinfo[link][alternate]."</a>";
		$page .="<br />";
		$page .="简介：";
		$page .=$this->dbinfo[summary];
		return $page;
	}
	function init(){
		foreach($this->dbarray as $db){
			switch($db["tag"]){
				case "DB:TAG":
					$this->dbinfo["tag"][] = $db["attributes"]["NAME"];
					break;
				case "DB:ATTRIBUTE":
					$this->dbinfo[$db["attributes"]["NAME"]][] = $db["value"];
					break;
				case "LINK":
					$this->dbinfo["link"][$db["attributes"]["REL"]] = $db["attributes"]["HREF"];
					break;
				case "TITLE":
					$this->dbinfo["name"] = $db["value"];
					break;
				case "NAME":
					$this->dbinfo["author"][] = $db["value"];
					break;
				case "SUMMARY":
					$this->dbinfo["summary"] = $db["summary"];
					break;
				case "GD:RATING":
					$this->dbinfo["rating"] = $db["rating"]["average"];
					break;
				default:
					break;
			}
		}
	}
	// function get_info($result)
	// {
		
	// 	//if..else.. 判断打开链接是否为空
 //        if ($obj = json_decode($result)){
	// 	    //将影片的信息放在全局数组中
	// 		$title = $obj->{'title'};
	// 		$author = $obj->{'author'};
	// 		$summary = $obj->{'summary'};
	// 		$ID=$obj->{'id'};
	// 		$link = $obj->{'link'};  
	// 		$gd = (array)$obj->{'gd:rating'};	
	// 		$db_array = array();
			
	// 		$db = $obj->{'db:attribute'};
	// 		//将db:atribute中的值放进数组
	// 		foreach ($db as $value){
	// 			$value_array = (array)($value);
	// 			$v = $value_array["@name"];
	// 			$k = $value_array["\$t"];
	// 			if (array_key_exists("@lang",$value_array)){
	// 			    $lang=$value_array["@lang"];
	// 				$k=$k.'['.$lang.']';
	// 			}		
	// 		    $db_array[$v][]=$k; 
	// 		}
 //            //设定对应数组的键值
	// 		@$db_array_key = array('【影片原名】','【别    名】','【导    演】','【编    剧】','【官方网站】','【IMDB链接】','【出品年代】',
	// 		                   '【国    家】','【电影类型】','【上映日期】','【放映长度】','【集    数】','【语    言】','【演    员】');
	// 		@$db_array_value = array($db_array["title"],$db_array["aka"],$db_array["director"],$db_array["writer"],$db_array["website"],
	// 		                     $db_array["imdb"],$db_array["year"],$db_array["country"],$db_array["movie_type"],$db_array["pubdate"],
	// 							 $db_array["movie_duration"],$db_array["episodes"],$db_array["language"],$db_array["cast"]);
	// 		@$db_array = array_combine($db_array_key,$db_array_value);
	//     }   else{
	// 	        echo "Empty link!";
	// 	}
	// 	$info=array("title"=>$title,"author"=>$author,"summary"=>$summary,"ID"=>$ID,"link"=>$link,"gd"=>$gd,"db_array"=>$db_array);
	// 	return $info;
	// }

	function json_to_xml($source,$charset='utf8') {  
		if(!($source)){  
			return false;  
		}  
		$array = json_decode($source);  //php5£¬ÒÔ¼°ÒÔÉÏ£¬Èç¹ûÊÇ¸üÔç°æ±¾£¬ÕÏÂÝdJSON.php  
		$xml  ='<!--l version="1.0" encoding="'.$charset.'-->';  
		$xml .= $this->change($array);  
		return $xml;  
	}  

	function change($source) {  
		$string=""; 
		foreach($source as $k=>$v){ 
		$string .="<".$k.">"; 
		if(is_array($v) || is_object($v)){       //ÅÐ¶ÏÊÇ·ñÊÇÊý×é£¬»òÕß£¬¶ÔÏñ 
			$string .= $this->change($v);        //ÊÇÊý×é»òÕß¶ÔÏñ¾ÍµÄµÝ¹éµ÷ÓÃ 
		}else{ 
			$string .=$v;                        //È¡µÃ±êÇ©Êý¾Ý 
		} 
		$string .="";  
		}
		return $string;  
	}
	function setid($imdb_id = 0,$type = "imdb"){
		if($type == "imdb")
			$this->siteurl = "http://api.douban.com/v2/movie/subject/imdb/tt";
		else if($type == "douban")
			$this->siteurl = "http://api.douban.com/v2/movie/subject/";

		if(file_exists($this->cachepath.$imdb_id.".xml")){

			$this->doubanxml = file_get_contents($this->cachepath.$imdb_id.".xml");
		}else{
			$json=file_get_contents($this->siteurl.$imdb_id."?apikey=".$this->apikey);
			$this->doubanxml =self::json_to_xml($json);

			file_put_contents($this->cachepath.$imdb_id.".xml",$this->doubanxml);
		}
		// $this->dbarray=self::get_info($this->doubanxml);
		// $this->init();
		$xmlparser = xml_parser_create();
		xml_parse_into_struct($xmlparser,$this->doubanxml,$this->dbarray);
		$this->init();
		file_put_contents($this->cachepath.$imdb_id.".page",$this->prinfo());
		@ copy($this->dbinfo[link][image],$this->imagepath.$imdb_id.".jpg");
	}
}
?>
