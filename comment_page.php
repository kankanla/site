<script type = "text/javascript">
onload = function(){
	comment_page_css_style();
	comment_page_hide_item();
	comment_page_add_main();
}

function comment_page_add_main(){
	//コメントを送信するためのエリアです。
	var x = document.getElementById('comment_page_add_main_area');
	var comment_area = document.createElement('textarea');
		//comment_area.cols="40";
		comment_area.rows ="5";
		comment_area.style.width="100%";
		comment_area.style.resize = "vertical";
		comment_area.id = "comment_page_add_main_area";
	x.appendChild(comment_area);
	
}

function comment_page_css_style(){
	var x = document.getElementsByClassName("comment_page_amin");
		for (var i = 0; i < x.length; i++){
			x[i].style.borderStyle = "solid";
		}
	
	var xx = document.getElementsByClassName("comment_page_sub");
		for (var ii = 0; ii < xx.length; ii++){
			xx[ii].style.borderStyle = "double";
			xx[ii].style.width = "85%";
			xx[ii].style.marginLeft = "auto";

		}
}

function comment_page_hide_item(){
		hide("comment_page_rowid");
		hide("comment_page_video_id");
		hide("comment_page_type");
		hide("comment_page_parent_rowid");
		hide("comment_page_user_ip");
		hide("comment_page_user_ip");
		hide("comment_page_sub_rowid");
		hide("comment_page_sub_type");
		hide("comment_page_sub_parent_rowid");
		hide("comment_page_sub_user_ip");
		hide("comment_page_sub_user_ip");
	function hide(e){
		var x = document.getElementsByClassName(e);
		for(var i = 0; i < x.length; i++){
			x[i].style.display = "none";
		}
	}
}



</script>
<?php
// comment page
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');

if(array_key_exists ('song',$_GET)){
}

$sh = new cmt_api();
$sh->debug();
$sh->add_main();
$sh->show_main('jj');


class cmt_api{
	public $db_name = 'comment_db.db';
	
	public function cmt_api(){
		
	}
	
	public function debug(){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$sql = "select count(*) from text_comment";
		$temp = $db->querySingle($sql);
		var_dump($temp);
		$db->close();
	}
	
	public function add_main(){
		echo "<div id = 'end_user_icon'>user.img</div>";
		echo "<div id = 'comment_page_add_main_area'></div>";
		echo "<div id = 'comment_page_add_main_cancel'>Cancle</div>";
		echo "<div id = 'comment_page_main_send'>Send</div>";

	}
	
	
	public function show_main($video_id){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare('select * from text_comment where video_id = :a and type = "m"');
		$temp->bindValue(':a',$video_id,SQLITE3_TEXT);
		$exec = $temp->execute();

		while($item = $exec->fetchArray(SQLITE3_ASSOC)){
			echo '<div class = "comment_page_amin">';
			echo "<div class = 'comment_page_rowid'>rowid->{$item['rowid']}</div>";
			echo "<div class = 'comment_page_video_id'>video_id->{$item['video_id']}</div>";
			echo "<div class = 'comment_page_type'>type->{$item['type']}</div>";
			echo "<div class = 'comment_page_parent_rowid'>parent_rowid->{$item['parent_rowid']}</div>";
			echo "<div class = 'comment_page_user_ip'>ip->{$item['ip']}</div>";
			echo "<div class = 'comment_page_user'>user_id->{$item['user_id']}</div>";
			echo "<div class = 'comment_page_date'>cre_date->{$item['cre_date']}</div>";
			echo "<div class = 'comment_page_comment'>comment->{$item['comment']}</div>";
			echo "<div class = 'comment_page_laud_count'>laud->{$item['laud_count']}</div>";
			echo "<div class = 'comment_page_minus_count'>minus->{$item['minus_count']}</div>";
			echo '</div>';
			$this->show_sub($item['rowid']);
		}
		$db->close();
	}
	
	public function show_sub($parent_rowid){
		$db = new sqlite3($this->db_name);
		$db->busyTimeout(10000);
		$temp = $db->prepare('select * from text_comment where parent_rowid = :a and type = "s"');
		$temp->bindValue(':a',$parent_rowid);
		$exe = $temp->execute();

		while($item = $exe->fetchArray(SQLITE3_ASSOC)){
			echo '<div class="comment_page_sub">';
			echo "<div class = 'comment_page_sub_rowid'>rowid->{$item['rowid']}</div>";
			echo "<div class = 'comment_page_sub_type'>type->{$item['type']}</div>";
			echo "<div class = 'comment_page_sub_parent_rowid'>parent_rowid->{$item['parent_rowid']}</div>";
			echo "<div class = 'comment_page_sub_user_ip'>ip->{$item['ip']}</div>";
			echo "<div class = 'comment_page_sub_user'>user->{$item['user_id']}</div>";
			echo "<div class = 'comment_page_sub_date'>date->{$item['cre_date']}</div>";
			echo "<div class = 'comment_page_sub_comment'>comment->{$item['comment']}</div>";
			echo "<div class = 'comment_page_sub_laud_count'>laud->{$item['laud_count']}</div>";
			echo "<div class = 'comment_page_sub_minus_count'>minus->{$item['minus_count']}</div>";
			echo '</div>';
			$this->show_sub($item['rowid']);
		}
		$db->close();
	}
	
	public function cmt_write_main($vid,$data){
		
	}
	
	public function com_write_sub($main_id,$data){
	
	}
	
	
}







?>