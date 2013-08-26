<?

class douban {
	var $doubanjson,$dbarray;
	var $dbinfo;
	var $cachepath = "",$siteurl = "",$apikey = "";
	function __construct() {
		$this->cachepath = "./imdb/cache/";
		$this->imagepath = "./imdb/images/";
		$this->apikey = "0022e42002ad5ae10068ac60080810a3";
   	}

	function prinfo(){
		$page = "";
		$page .= "资源名称：";
		$page .= $this->dbinfo['title'];
		$page .="（";
		foreach($this->dbinfo['aka'] as $key => $value)
		{			
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
		foreach($this->dbinfo['casts'] as $key => $value){
			if($key > 0)
				$page .="，".$value['name'];
			else
				$page .= $value['name'];
		}
		/*	api v2 中消失
		$page .="<br />";
		$page .="其他演员：";
		foreach($this->dbinfo['cast'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		*/
		$page .="<br />";
		$page .="电影类型：";
		foreach($this->dbinfo['genres'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}/*
		$page .="<br />";
		$page .="电影语言：";
		foreach($this->dbinfo['languages'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}*/
		$page .="<br />";
		$page .="产　　地：";
		$page .=$this->dbinfo['countries'][0];
		$page .="<br />";
		$page .="导　　演：";
		$page .=$this->dbinfo['directors'][0]['name'];
		$page .="<br />";
		$page .="年代：";
		$page .=$this->dbinfo['year'];
		$page .="<br />";/*
		$page .="电影时长：";
		$page .=$this->dbinfo['durations'][0];
		*/
		//$page .="<br />";
		/* api v2 中消失
		$page .="豆瓣标签：";
		foreach($this->dbinfo['tag'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .=$value;
		}
		*/
		$page .="<br />";
		$page .="豆瓣评分：";
		$page .=$this->dbinfo['rating']['average'];
		$page .="<br />";
		$page .= "豆瓣链接：";
		$page .= "<a href=\"".$this->dbinfo['alt']."\" target=\"_blank\">".$this->dbinfo['alt']."</a>";
		$page .="<br />";
		$page .="简介：";
		$page .=$this->dbinfo['summary'];
		return $page;
	}

	function init(){
		foreach($this->dbarray as $db){

					$this->dbinfo["title"][] = $db["title"];
					$this->dbinfo["aka"] = $db["aka"];
					$this->dbinfo["link"][$db["attributes"]["REL"]] = $db["attributes"]["HREF"];
					$this->dbinfo["casts"]["name"] = $db["casts"]["name"];
					$this->dbinfo["genres"][] = $db["genres"];
					$this->dbinfo["summary"] = $db["summary"];
					$this->dbinfo["rating"] = $db["rating"]["average"];
		}
	}

	function setid($imdb_id = 0,$type = "imdb"){
		if($type == "imdb")
			$this->siteurl = "http://api.douban.com/v2/movie/subject/imdb/tt";
		else if($type == "douban")
			$this->siteurl = "http://api.douban.com/v2/movie/subject/";

		if(file_exists($this->cachepath.$imdb_id.".json")){

			$this->doubanjson = file_get_contents($this->cachepath.$imdb_id.".json");
		}else{
			$this->doubanjson=file_get_contents($this->siteurl.$imdb_id."?apikey=".$this->apikey);

			file_put_contents($this->cachepath.$imdb_id.".json",$this->doubanjson);
		}
		$this->dbinfo=json_decode($this->doubanjson,1);
		//$this->init();
		file_put_contents($this->cachepath.$imdb_id.".page",$this->prinfo());
		@ copy($this->dbinfo[images][medium],$this->imagepath.$imdb_id.".jpg");
	}
}
?>
