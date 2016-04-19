<?php
//include_allartist.php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'sinclude_allartist.php'){
	include_once('404.php');
}else{
	include_once('aixin.php');
}

//debug 項目
///////////////////////////////////////
if(DEBUG){echo '<pre><br>';}
if(DEBUG){echo '<br> #011 >><br> '; echo __FILE__;}
if(DEBUG){echo '<br> #012 >><br> ';var_dump($_REQUEST);}

//
///////////////////////////////////////
if(ADSENSE){include_once('include_adsense.php');}
adsense_area1();

//
///////////////////////////////////////////////
$all_at = new allartist();
$all_at->artist();

//
///////////////////////////////////////////////
class allartist{

	function allartist (){	
	}

	function artist(){
		$include_allartist_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?search_query=';
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(10000);
		$sql = $db->prepare("select * from lid where title !='' order by title ASC");
		$sql_data = $sql->execute();
		$temps = array();
		echo '<div id = "item_card_area">';
		while($temp = $sql_data->fetchArray(SQLITE3_ASSOC)){
			$con = count($temps);
			$title = urlencode($temp['title']);
			echo '<div class = "item_cards">';
			echo '<div class = "item_cards_filter"></div>';
			echo "<div class = \"allartist_video_id\"><img class = \"item_cards_img\" src = \"http://i1.ytimg.com/vi/{$this->fast_item($temp['list'])}/mqdefault.jpg\" alt = \"{$temp['title']}\" ></div>";
			echo "<div class = \"allartist_title\"><a href = \"{$include_allartist_url}{$title}\">{$temp['title']}</a></div>";
			echo '</div>';
		}
		echo '</div>';
		$db->close();
	}
	
	function fast_item($list){
		//LIDの最初のVIDをリターン
		//////////////////////////////////////////////
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(10000);
		$sql = $db->prepare("select rowid from lid where list = :a");
		$sql->bindValue(':a',$list,SQLITE3_TEXT);
		$temp = $sql->execute();
		$temp_data = $temp->fetchArray(SQLITE3_ASSOC);
		//return $temp_data['rowid'];
		$sql2 = $db->prepare("select video_id from vid_lid where LID_rowid = :a limit 1");
		$sql2->bindValue(':a',$temp_data['rowid'],SQLITE3_TEXT);
		$temp2 = $sql2->execute();
		$temp2_data = $temp2->fetchArray(SQLITE3_ASSOC);
		$db->close();
		return $temp2_data['video_id'];
	}
}
?>
<script type = "text/javascript">
//include_allartist.php
item_cards_event();
function item_cards_event(){
	var x = document.getElementsByClassName('item_cards');
	for(var i = 0; i<x.length; i++){
		x[i].addEventListener("mouseover",function(e){
			this.style.boxShadow = "2px 2px 15px";
			this.style.top = "-3px";
			this.style.left = "-3px";
			this.firstElementChild.style.backgroundColor = "rgba(230, 230, 250, 0)";
		},false);
		
		x[i].addEventListener("mouseout",function(e){
			this.style.boxShadow = "1px 1px 1px";
			this.style.top = "0px";
			this.style.left = "0px";
			this.firstElementChild.style.backgroundColor = "rgba(230, 230, 250, 0.2)";
		},false);
		
		x[i].addEventListener("click",function(){
			location.href = location.origin + location.pathname +'?search_query=' + this.innerText;
		},false);
	}
}
</script>