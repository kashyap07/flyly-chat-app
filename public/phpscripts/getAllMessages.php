<?php
	require 'db_connect.php';
	$recieverID = $_POST['uid'];
	$friendName = $_POST['name'];
	$time = $_POST['time'];
	$n = 10;
	if(isset($_POST['n'])) {
		$n = $_POST['n'];
	}
	// Query to retrieve the latest message that was sen by another person----------------------------------------------
	$senderID = -1 ; // SOME random val to intialize with.
	// prepare statement.
	if(!$stmnt = $connection->prepare('SELECT userid FROM `users` where username = ?')){
		//echo "ohh no";
		die(json_encode(array(
			 'status'=>'error',
			 'message'=>'query failed!'
		)));
	}
	//binding the '?' with $friendName.
	 if(!$stmnt->bind_param('s',$friendName)){
		die(json_encode(array(
			'status'=>'error',
			'message'=>'binding unsuccessful!'
		)));
	}
	//executing the statement.
	if(!$stmnt->execute()){
		die(json_encode(array(
			'status'=>'error',
			'message'=>'execution falied!'
		)));
	}
	// binding the result of the query to $senderID
	if(!$stmnt->bind_result($senderID)){
		die(json_encode(array(
			'status'=>'error',
			'msg'=>'binding failed'
		)));
	}
	$res = $stmnt->fetch();
	if($res == TRUE){
		//echo $senderID;
	}
	else if($res == NULL) {
		die(json_encode(array(
			'status'=>'Could not fetch the sender userID',
			'message'=>'execution falied!'
		)));
	}
	else{
		die(json_encode(array(
			'status'=>'Erro Occured while fetching',
			'message'=>'execution falied!'
		)));
	}
	//motherfucker.
	$stmnt->close();

	######This much working perfect.
	$stmnt2 = $connection->prepare('SELECT body, msgTime FROM messages where ((send_id = ? and rcv_id = ?) OR (send_id = ? and rcv_id = ?)) and msgTime <= FROM_UNIXTIME(?) ORDER BY msgTime DESC LIMIT ?');
	if(!$stmnt2) {
		echo "hello there";
		die(json_encode(array(
			'status'=>'error in second part',
			'message'=>'query failed! in second part'
		)));
	}
	$latestmessage = '';
	if(!$stmnt2->bind_param('iiiiii', $senderID, $recieverID, $recieverID, $senderID, $time, $n)){
		die(json_encode(array(
			'status'=>'error in second part',
			'message'=>'binding unsuccessful in second part!'
		)));
	}
	if(!$stmnt2->execute()){
		die(json_encode(array(
			'status'=>'error in second part',
			'message'=>'execution falied! in second part'
		)));
	}
	if(!$stmnt2->bind_result($latestmessage)){
		die(json_encode(array(
			'status'=>'error',
			'msg'=>'binding failed'
		)));
	}
	/*$res = $stmnt2->fetch();
	if($res == TRUE){
		#echo $latestmessage;
	}
	else if($res == NULL){
		die(json_encode(array(
			'status'=>'Could not fetch the sender userID',
			'message'=>'execution falied!'
		)));
	}
	else{
		die(json_encode(array(
			'status'=>'Error Occured while fetching',
			'message'=>'execution falied!'
		)));
	}*/
	$messages = array();
	while($stmnt2->fetch()) {
		$messages[] = $latestmessage;
	}
	//you too.
	$stmnt2->close();
	//close all the fucking stmnts.
	
	die(json_encode(array(
		'status' => 'success',
		'msgs' => $messages
	)));
?>