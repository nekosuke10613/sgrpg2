<?php
/**
 * MySQLに接続しデータを取得する
 *
 */

// 以下のコメントを外すと実行時エラーが発生した際にエラー内容が表示される
// ini_set('display_errors', 'On');
// ini_set('error_reporting', E_ALL);

//-------------------------------------------------
// ライブラリ
//-------------------------------------------------
require_once("../util.php");
require_once("../../model/user.php");

//-------------------------------------------------
// 引数を受け取る
//-------------------------------------------------
$token = UserModel::getTokenfromQuery();

if( !$token ){
  sendResponse(false, 'Invalid token');
  exit(1);
}

//-------------------------------------------------
// SQLを実行
//-------------------------------------------------
try{
  $user = new UserModel();
  $uid  = $user->getUserIdByToken($token);
  if( $uid !== false ){
    $buff = $user->getRecordById($uid);
  }
  else{
    $buff = false;
  }
}
catch( PDOException $e ) {
  sendResponse(false, 'Database error: '.$e->getMessage());  // 本来エラーメッセージはサーバ内のログへ保存する(悪意のある人間にヒントを与えない)
  exit(1);
}

//-------------------------------------------------
// 実行結果を返却
//-------------------------------------------------
// データが0件
if( $buff === false ){
  sendResponse(false, 'Not Found user');
}
// データを正常に取得
else{
  sendResponse(true, $buff);
}

