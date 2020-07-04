<?php
//-------------------------------------------------
// 定数
//-------------------------------------------------
define('DB_DSN',  'mysql:dbname=sgrpg;host=127.0.0.1');  // 接続先を定義
define('DB_USER', 'senpai');      // MySQLのユーザーID
define('DB_PW',   'indocurry');   // MySQLのパスワード


/**
 * ユーザーIDを取得する
 */
function getQueryUserID(){
  $uid = isset($_GET['uid'])?  $_GET['uid']:null;

  if( ($uid === null) || (!is_numeric($uid)) ){
    return(false);
  }
  else{
    return($uid);
  }
}


/**
 * DBに接続する
 *
 * @return Object $dbh
 */
function connectDB(){
  $dbh = new PDO(DB_DSN, DB_USER, DB_PW);   // 接続
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // エラーモード

  return($dbh);
}


/**
 * SQLを実行する
 *
 * @param Object $dbh
 * @param String $sql
 * @param Array  $bind  [ ['name'=>xxx, 'value'=>xxx, 'type'=>xxx], [...], [...] ]
 * @return Object $sth
 */
function query($dbh, $sql, $bind=null){
  $sth = $dbh->prepare($sql);
  
  if( $bind !== null ){
    for($i=0; $i<count($bind); $i++){
	   $sth->bindValue($bind[$i]['name'], $bind[$i]['value'], $bind[$i]['type']);
	 }
  }

  $sth->execute();
  return($sth);
}


/**
 * 実行結果をJSON形式で返却する
 *
 * @param boolean $status
 * @param array   $value
 * @return void
 */
function sendResponse($status, $value=[]){
  header('Content-type: application/json');
  echo json_encode([
    'status' => $status,
    'result' => $value
  ]);
}
