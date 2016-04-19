<?php
//include_main_document.php
//include_main.php
if(count(get_included_files()) == 1 or basename($_SERVER['SCRIPT_FILENAME']) == 'include_main_document.php'){
	include_once('404.php');
}else{
	include_once('aixin.php');
}


//debug 項目
///////////////////////////////////////
// if(DEBUG){echo '<pre>';}
if(DEBUG){echo '#013 >> '; echo __FILE__;}
if(DEBUG){echo '#014 >> ';var_dump($_REQUEST);}

//
///////////////////////////////////////
if(ADSENSE){include_once('include_adsense.php');}

//
/////////////////////////////////////////
$include_main_document_show = new main_document_search();

//本ページ
/////////////////////////////////////////
echo '<div id ="include_main_area">';
	echo '<div id = "include_main_document">';
	
		//ADSE 広告選択
		////////////////////////////////////////	
		// if(ADSENSE){adsense_area2();};

		$video_chk = $include_main_document_show->youtube_player_chk();

		if($video_chk){
			echo '#2016/04/20 1:10:19';
			echo 'player_area';
			print_r($video_chk);
			$include_main_document_show->youtube_player($video_chk[0]['vidio_id'],'test');

		}else{
			//検索のキーワードから再生リストのTitleと一致するリストを検索します。
			//また、そのリストに含まれるすべてのVIDEOIDとVIDEOのタイトルを表示します。
			////////////////////////////////////////
			$include_main_document_show->from_lid(urldecode(trim($_REQUEST['search_query'])));
		
			//Video リクエスト
			////////////////////////////////////////
			$include_main_document_show->from_vid(urldecode(trim($_REQUEST['search_query'])));
		}


	echo '</div>';	//include_main_center

	echo '<div id = "include_main_right">';
		//右バー 未定
		////////////////////////////////////////
		$include_main_document_show->debug(111);
	echo '</div>'; //include_main_right
	
echo '</div>';	//include_main_area

if(DEBUG){
		print_r($include_main_document_show->show_lid_array);
		print_r($include_main_document_show->show_vid_array);
}


//
///////////////////////////////////////////
class main_document_search{

	public $show_lid_array = array();
	public $show_vid_array = array();
	
	public function main_document_search(){

	}
	
////////////////////////////////////////////////////////////

//	キーワードからLIST情報を検索します。

////////////////////////////////////////////////////////////
	public function from_lid($req_key){
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(10000);
		$temp = $db->prepare('select * from lid where title like :a');
		$temp->bindValue(':a','%'.$req_key.'%',SQLITE3_TEXT);
		$temp2 = $temp->execute();
		
		echo '<div id ="S0110_from_lid">';
			while($val = $temp2->fetchArray(SQLITE3_ASSOC)){
				if(DEBUG){echo "#069\n";
					print_r($val);
				}
				
				$this->show_lid_array[] = $val['list'];
				$list_fast_videoid = $this->fast_item($val['list']);
				$rowid = $val['rowid'];
				$title = $val['title'];
				$list = $val['list'];
				$eng_name = $val['eng_name'];
				$comment = $val['comment'];
				//StyleSheet main #00074"
				echo '2016/04/17 1:43:27';
				echo '<div class = "S0100_main_document_artist_card">';
					echo '<div class = "S0102_main_artist_top_left">';
						echo "<img src = \"http://i1.ytimg.com/vi/{$list_fast_videoid}/mqdefault.jpg\" title = \"{$title}\" alt=\"{$title}\">";
						if(ADSENSE){adsense_area2();};
					echo '</div>';		//"S0102_main_artist_top_left"
					echo '<div class ="S0101_main_document_artist_info">';
						echo "<div class = \"from_lid_title\">{$title}</div>";
						echo "<div class = \"from_lid_eng_name\">英文名: {$eng_name}</div>";
						echo "<div class = \"from_lid_comment\">{$comment}</div>";
					echo '</div>';		//"S0101_main_document>artist_info"
				echo '</div>';		//"main_document_artist_card"
				$this->lid_rowid_vid($rowid);
			}
		echo '</div>';		//"S0110_from_lid"
		// echo '<pre>';
		echo 'pagecmd<br>';
		echo count($this->show_lid_array);
		$db->close();	
	}
	
	private function lid_rowid_vid($lid_rowid){
		// VIODEのDBから同じLID_ROWIDを持つVIDEOを検索します。
		// select video_id from vid_lid where lid_rowid = $lid_rowid
		// 20160316
		////////////////////////////////////////
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(10000);
		$sql_cmd = $db->prepare('select video_id from vid_lid where lid_rowid = :a');
		$sql_cmd->bindValue('a',$lid_rowid,SQLITE3_TEXT);
		$temp = $sql_cmd->execute();
		if(DEBUG){echo '2016/04/17 1:48:14';}

		echo '<div class = "S0103_mini_card_area">';
			// 2016/04/17 21:52:21
			while ($val = $temp->fetchArray(SQLITE3_ASSOC)){
				echo '<div class = "S0104_mini_card">';
				if(DEBUG){echo '2016/04/17 21:52:51';}
				if(DEBUG){print_r($val);}
					$title = $this->video_id_title ($val['video_id']);
					// htmlspecialchars 2016/04/17 1:56:59
					$title = htmlspecialchars($this->video_id_title ($val['video_id']));
					$a_url =  $_SERVER["SCRIPT_NAME"].'?search_query='.urlencode($this->video_id_title ($val['video_id']));
					echo "<div class = \"S0204_mini_card_img\"> <a href=\"{$a_url}\" title=\"{$title}\"><img src=\"http://i1.ytimg.com/vi/{$val['video_id']}/mqdefault.jpg\" alt=\"{$title}\"></a></div>";
					echo "<div class = \"S0203_mini_card_title\"><a href=\"{$a_url}\" title=\"{$title}\"><p>{$title}</p></a></div>";
				echo '</div>';
			}
		echo '</div>';
		$db->close();
		//http://i1.ytimg.com/vi/{$val['video_id']}/mqdefault.jpg
	}
	
	
	private function fast_item($list){
		//LIDに含まれる動画のFast動画IDをリターン
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
	

	private function video_id_title ($vid){
		// VIDEOIDからVIDEOのタイトルをリターンする。
		// select title from vid where video_id = $vid
		// 20160316
		//////////////////////////////////////////
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(10000);
		$sql_cmd = "select title from vid where video_id ='{$vid}'";
		return $db->querySingle($sql_cmd);
		$db->close();
	}
	
	
	
////////////////////////////////////////////////////////////
	
// 検索キーワードからVIDEOID情報を検索します。
// 2016/04/17 23:55:52
////////////////////////////////////////////////////////////
	public function from_vid($req_key){
		if(count($this->show_lid_array) == 0){
			$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
			$db->busyTimeout(10000);
			if(DEBUG){echo '#2016/04/17 14:05:55';}
			$temp = $db->prepare('select * from vid where title like :a');
			$temp->bindValue(':a','%'.$req_key.'%',SQLITE3_TEXT);
			$temp2 = $temp->execute();
			$base_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?search_query=';
			// http://i1.ytimg.com/vi/{}/mqdefault.jpg
				while($val = $temp2->fetchArray(SQLITE3_ASSOC)){
					// 檢索到的視頻內容，會有多重結果
					// <a href="" title=""></a>
					// <img src="" alt="">
					echo '<div class ="S0201_main_player_area">';
							//$this->show_vid_array[] = $val['video_id'];
// 2016/04/18 1:21:58
							$a_url = $base_url.urlencode($val['title']);
							$ttile = htmlspecialchars($val['title']);
							echo "<div class = \"S0205_main_player_img\"><img src=\"http://i1.ytimg.com/vi/{$val['video_id']}/mqdefault.jpg\" alt=\"{$ttile}\"></div>";
							echo "<div class = \"S0206_main_player_text\"><a href=\"{$a_url}\" title=\"{$ttile}\"><p>{$ttile}</p></a></div>";

							if(DEBUG){print_r($val);}
					echo '</div>';	//<div id ="S0201_main_player_area">

					echo '<div class ="S0202_main_list_item_area">';
					// 包含此視頻的列表ID，一個視頻可能會包含在多個視頻列表內
							if(DEBUG){print_r($val);}
							if(DEBUG){echo '#2016/04/17 14:08:20';}
							foreach ($this->req_vid_inlid($val['video_id']) as $key => $vval){
								// 包含此視頻的列表ID 2016/04/17 20:28:01
								echo $vval;
								echo '<br>';
							}
					echo '</div>'; //S0201_main_list_item_area
				}

			echo '#0154<br>';
			echo (count($this->show_vid_array));
		
			$db->close();
		}
	}



	
	// リストIDからすべてのVIDEOIDを検索します。
	// 03/17
	////////////////////////////////////////////////////////////
	public function videos_from_lid($lid){
		if(count($this->show_vid_array) < 5){
			$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
			$db->busyTimeout(10000);
			$sql_cmd = $db->prepare('select rowid from lid where list = :a');
			$sql_cmd->bindValue(':a',$lid,SQLITE3_TEXT);
			$temp = $sql_cmd->execute();
			
			while($val = $temp->fetchArray(SQLITE3_ASSOC)){
				echo '#0174';
				print_r($val);
				$this->lid_roid_tovids($val['rowid']);   //lid_roid_tovids function
			}
		}
	}
	
	private function lid_roid_tovids($lid_rowid){
		if(DEBUG){echo '#2016/04/17 20:12:09';}
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(10000);
		$sql_cmd = $db->prepare('select video_id from vid_lid where "lid_rowid" = :a');
		$sql_cmd->bindValue(':a',$lid_rowid,SQLITE3_TEXT);
		$temp = $sql_cmd->execute();
		
		while($val = $temp->fetchArray(SQLITE3_ASSOC)){
				echo '#0188';
				//print_r($val);
				echo ($val['video_id']);
				echo '<br>';
				echo $this->video_id_title($val['video_id']);
				echo '<br>';
		}
	}
	
	
	
	
	private function req_vid_inlid($vid){
// 2016/04/19 23:45:42
		//VIDから所属するLID_ROWIDをリターン
		//03/17
		///////////////////////////////////////
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(1000);
		$sql_cmd = $db->prepare('select LID_rowid from vid_lid where video_id = :a');
		$sql_cmd->bindValue(':a',$vid,SQLITE3_TEXT);
		$temp = $sql_cmd->execute();

		$req_data = array();

		print_r($req_data);
			while($val = $temp->fetchArray(SQLITE3_ASSOC)){
				foreach($val as $key => $vval){
					$req_data[count($req_data)] = $this->req_lid($vval);
				}
			}

		echo '#2016/04/17 17:58:12<br>';
		if(DEBUG){var_dump($req_data);}
		$db->close();
		return array_unique ($req_data);
	}
	
	
	private function req_lid($lid){
		//listのROWIDからリストIDを検索しリターンする。
		///////////////////////////////////////
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(1000);
		$sql_cmd = $db->prepare('select list from lid where rowid = :a');
		$sql_cmd->bindValue(':a',$lid,SQLITE3_TEXT);
		$temp = $sql_cmd->execute();
		// $req_data = array();
		while($val = $temp->fetchArray(SQLITE3_ASSOC)){
			// $req_data[count($req_data)] = $val['list'];
			$req_data = $val['list'];
		}
		$db->close();
		return $req_data;
	}
	
	public function youtube_player_chk(){
// 2016/04/20 1:07:38
		// 再生プレーヤを追加
		if(DEBUG){echo '#2016/04/20 0:25:36 function youtube_player()';}
		$db = new sqlite3(DB_NAME,SQLITE3_OPEN_READONLY);
		$db->busyTimeout(10000);
		$sql_select = $db->prepare('select video_id, title from vid where title = :a');
		$sql_select->bindValue(':a',$_GET['search_query']);
		$temp = $sql_select->execute();
		$req_vid = array();
		while($val = $temp -> fetchArray(SQLITE3_ASSOC ) ) {
			if(DEBUG){echo '<pre>'; print_r($val);};
			$count = count($req_vid);
			$req_vid[$count]['vidio_id'] = $val['video_id'];
			$req_vid[$count]['title'] = $val['title'];
		}

		$db->close();
		return $req_vid;
	}

	public function youtube_player($vid, $lid){
// 2016/04/20 1:13:42
		 // 再生プレーヤを追加
		echo "<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/{$vid}\" frameborder=\"0\" allowfullscreen></iframe>";

	}


	
	
	public function debug($q){
		//var_dump($_REQUEST);
			for($i=0; $i<$q; $i++){
				echo 'default';
				echo $i.'<br>';
			}	
	}
}
?>