<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');



$j = new myapi();

if(array_key_exists('getlists',$_REQUEST)){
	//http://192.168.11.10/youtube_db/api.php?getlists
	$j->getlists();
}

if(array_key_exists('lyrics',$_REQUEST)){
	// 
	$j->lyrics();
}

if(array_key_exists('search_query',$_REQUEST)){
	//http://192.168.11.10/youtube_db/search.php?search_query=朋友
	$j->song($_REQUEST['search_query']);
}

if(array_key_exists('title',$_REQUEST)){
	//2015/11/26
	//Titleを検索し、LISTIDとVIDEOIDSをReturn.
	//http://192.168.11.10/youtube_db/search.php?title=広瀬香美
	$j->title($_REQUEST['title']);
}


if(array_key_exists('keyword',$_REQUEST)){
	//http://192.168.11.10/youtube_db/search.php?keyword=梁文音
	//$j->artist($_REQUEST['keyword']);
	$j->song($_REQUEST['keyword']);
}

if(array_key_exists('list',$_REQUEST)){
	
	$j->artist_list();
}

if(array_key_exists('rand',$_REQUEST)){
	if($_REQUEST['rand'] == '198'){
		//$j->sub_list_ajax($_REQUEST['q']);
		$j->sub_list_ajax(urldecode($_REQUEST['q']));
	}
}



class myapi{
	
	public $db_name = 'youtube_db.db';
	
	function myapi(){
		
	}
	
	function sub_list_ajax($q){
		//キーワード検索 sub_list_ajax rand = 198
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		
		//from list id
		//$sql = $db->prepare("select rowid,* from lid where title like :a or eng_name like :a or pinyin_name like :a");
		$sql = $db->prepare("select rowid,* from lid where title like :a or eng_name like :b or pinyin_name like :c");
		$sql->bindValue(':a','%'.$q.'%',SQLITE3_TEXT);
		$sql->bindValue(':b','%'.$q.'%',SQLITE3_TEXT);
		$sql->bindValue(':c',$q.'%',SQLITE3_TEXT);
		$sql_data = $sql->execute();
		$temps=array();

		while($temp = $sql_data->fetchArray(SQLITE3_ASSOC)){
			$con = count($temps);
			$temps[$con]['items'] = urldecode($temp['title']);
		}

		//from video id
		$sql2 = $db->prepare("select rowid,* from vid where title like :a");
		$sql2->bindValue(':a','%'.$q.'%',SQLITE3_TEXT);
		$sql2_data = $sql2->execute();
		
		while($temp = $sql2_data->fetchArray(SQLITE3_ASSOC)){
			$con = count($temps);
			//$temps[$con]['items'] = urldecode($temp['title']);
			//2013/04/04 エンコード修正
			//$temps[$con]['items'] = str_replace('&amp;','%26',$temp['title']);
			$temps[$con]['items'] = htmlspecialchars_decode($temp['title']);
		}

		echo json_encode($temps);
		$db->close();		
	}
	
	
	
	function getlists(){
		//すべてのリストを表示し、タイトルをソートする
		//
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$sql = $db->prepare("select rowid,* from LID where active = '0' ORDER BY title desc");
		$sql_a = $db->prepare("select count(*) from VID_LID where LID_rowid = :a and active = '0'");
		
		$sql_exec = $sql->execute();
		$temp = array();
		while($x = $sql_exec->fetchArray(SQLITE3_ASSOC)){
			$sql_a->bindValue(':a',$x['rowid']);
			$sql_a_exec = $sql_a->execute();
			$count = count($temp);
			$temp[$count]['info'] = $x;
			$temp[$count]['count'] = $sql_a_exec->fetchArray(SQLITE3_ASSOC);
		}

		//echo json_encode($temp,JSON_FORCE_OBJECT);
		echo '<pre>';
		echo print_r($temp,JSON_FORCE_OBJECT);
		$db->close();
	}
	
	
	function artist_list($sort=null){
		// 列出所有的Title（artist）

		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		if(is_null($sort)){
			$sql_a = $db->prepare('select rowid,* from lid ORDER BY title DESC');
		}else{
			$sql_a = $db->prepare('select rowid,* from lid ORDER BY title ASC');
		}
		//$sql_a->bindValue(':a','title '.$sort,SQLITE3_TEXT);
		$sql_a_execute = $sql_a->execute();
		
		
		
		$temps = array();
		while($temp = $sql_a_execute->fetchArray(SQLITE3_ASSOC)){
			$cn = count($temps);
			$temps[$cn]['title'] = $temp['title'];
			$temps[$cn]['list'] = $temp['list'];
			$temps[$cn]['video'] = $this->f_lid_videoids($temp['rowid'])[0];
		}
		$db->close();
		//2015/11/27
		//JSON OK
		echo json_encode($temps,JSON_FORCE_OBJECT);
		//echo '<pre>';
		//print_r($temps);
		//echo json_last_error();
	}
	
	
	
	
	function title($p){
		//2015/11/26
		// artist == title
		// 1. GET LIST ID lid
		// 2. GET VID
		//http://192.168.11.10/youtube_db/api.php?title=広瀬香美
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		
		//get lid
		$sql_a = $db->prepare("select rowid,list,title from lid where title = :a");
		$sql_a->bindValue(':a',$p,SQLITE3_TEXT);
		$sql_a_execute = $sql_a->execute();
		
		$temps = array();
		while($temp = $sql_a_execute->fetchArray(SQLITE3_ASSOC)){
			
			$con = count($temps);
			$temps[$con]['rowid'] = $temp['rowid'];
			$temps[$con]['title'] = $temp['title'];
			$temps[$con]['list'] = $temp['list'];
			$temps[$con]['video_ids'] = $this->f_lid_videoids($temp['rowid']);
		}
		
		$db->close();
		echo '<pre>';
		print_r($temps);
		//JSON -OK
		//echo json_encode($temps);
		
	}
	
	
	
	function song($p){
		//2015/11/26
		//http://192.168.11.10/youtube_db/api.php?song=朋友
		//Song 情報を所得
		//所属リストIDを取得
		//リストに含まれる曲数を所得
		//[video_id]
		//[title]
		//[youku_id]
		//[lid]
		
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);		

		$sql_a = $db->prepare("select * from vid where title like :a");
		$sql_a->bindValue(':a','%'.$p.'%',SQLITE3_TEXT);
		$sql_a_execute = $sql_a->execute();
		$temps=array();
		
		while($temp = $sql_a_execute->fetchArray(SQLITE3_ASSOC)){
			$con = count($temps);
			$temps[$con]['title'] = urldecode($temp['title']);
			$temps[$con]['video_id'] = $temp['video_id'];
			$temps[$con]['list'] = $this->f_vid_listid($temp['video_id']);
		}
		
		$db->close();
		
		if(count($temps) > 0){
			echo '<pre>';
			print_r($temps);
			//echo json_encode($temps);
		}
		
	}
	
	function f_vid_listid($vid){
		//2015/11/26
		//VideoID所在的視頻列表ListID。
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$sql = $db->prepare("select list,title from lid where rowid in(select LID_rowid from vid_lid where video_id = :a)");
		$sql->bindValue(':a',$vid,SQLITE3_TEXT);
		$sql_execute = $sql->execute();
		$temps = array();
		while($temp = $sql_execute->fetchArray()){
			$con = count($temps);
			$temps[$con]['list'] = urldecode($temp['list']);
			$temps[$con]['title'] = $temp['title'];
		}
		$db->close();
		return $temps;
	}
	
	function f_lid_videoids($list_rowid){
		// 2015/11/26
		// 返回播放列表（LID）內所包含的視頻（Viode ID）
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$sql_a = $db->prepare("select video_id,title from vid where video_id in (select video_id from vid_lid where LID_rowid = :a)");
		$sql_a->bindValue(':a',$list_rowid,SQLITE3_TEXT);
		$sql_a_execute = $sql_a->execute();
		$temps = array();
		while($temp = $sql_a_execute->fetchArray(SQLITE3_ASSOC)){
			$con = count($temps);
			$temps[$con]['video_id'] = $temp['video_id'];
			$temps[$con]['title'] = urldecode($temp['title']);
		}
		$db->close();
		return $temps;
	}

}

?>