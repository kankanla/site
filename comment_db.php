<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');
print_r(SQLite3::version());
exit;
$db_name = 'comment_db.db';
if(!file_exists($db_name)){
	$db = new sqlite3($db_name);
	$db->busyTimeout(10000);
	$create_table = "create table main_comment(
					video_id text,
					comment text no null,
					cre_date text default current_timestamp,
					up_date text default current_timestamp,
					ip text default '0',
					user_id text default 'none',
					laud_count integer default 0,
					minus_count integer default 0,
					level text default '5'
					)";
	$db->exec($create_table);
	
	$create_table = "create table sub_comment(
					video_id text,
					main_rowid text,
					sub_comment text no null,
					up_date text default current_timestamp,
					ip text default '0',
					user_id text default 'none',
					laud_count integer default 0,
					minus_count integer default 0,					
					level text default '5'	
	)";
	$db->exec($create_table);
	
	$db->close();

}else{
	
	$db = new sqlite3($db_name,SQLITE3_OPEN_READONLY);
	echo '<br> main_comment count  ';
	$sql = 'select count(*) from main_comment';
	echo $db->querySingle($sql);
	
	echo '<br> sub_comment count  ';
	$sql = 'select count(*) from sub_comment';
	echo $db->querySingle($sql);
	$db->close();
	
	
		echo '<br><br>exists';
}
exit;



?>