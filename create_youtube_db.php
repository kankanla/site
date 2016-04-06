<?php
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');
print_r(SQLite3::version());


//{"video_id":"fqMlRNNHd6I","author":"KANKAN","title":"Musashino Line","list":"PL2GI1whQs2Q8zKMjDIu72OGjb6aZFYpK8"}

$db_name = 'youtube_db.db';
if(!file_exists($db_name)){
		$db = new sqlite3($db_name);
		$db->busyTimeout(10000);
		$create_table = 
		"create table VID(
			rowid INTEGER PRIMARY KEY AUTOINCREMENT,
			video_id text UNIQUE,
			author text,
			title text,
			Creationdate text default CURRENT_DATE,
			artist text default 'none',
			active text default '0',
			level text default '5'
		)";

		
		$db->exec($create_table);
		
		$create_table = 
		"create table LID (
			rowid INTEGER PRIMARY KEY AUTOINCREMENT,
			list text UNIQUE,
			title text,
			eng_name text,
			pinyin_name text,
			comment text,
			opencount INTEGER,
			Creationdate text default CURRENT_DATE,
			updatedate text default CURRENT_DATE,
			active text default '0',
			level text default '5'
		)";
		
		// active 0 公開 3非公開
		// 
		
		
		$db->exec($create_table);
		
		$create_table = 
		"create table VID_LID (
			rowid INTEGER PRIMARY KEY AUTOINCREMENT,
			LID_rowid text,
			video_id text,
			active text default '0'
		)";
		
		// active 0 公開 2 広告 3非公開
		$db->exec($create_table);
		
		$db->close();

}else{
		$db = new sqlite3($db_name,SQLITE3_OPEN_READONLY);
		echo '<br> VID count  ';
		$sql = 'select count(*) from VID';
		echo $db->querySingle($sql);
		
		echo '<br> LID count  ';
		$sql = 'select count(*) from LID';
		echo $db->querySingle($sql);
	
		echo '<br> VID_LID count  ';
		$sql = 'select count(*) from VID_LID';
		echo $db->querySingle($sql);	
	
		echo '<br><br>exists';
}
exit;

?>