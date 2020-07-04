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

//-------------------------------------------------
// 引数を受け取る
//-------------------------------------------------
$uid = getQueryUserID();

if( !$uid ){
  sendResponse(false, 'Invalid uid');
  exit(1);
}

//-------------------------------------------------
// 準備
//-------------------------------------------------
// 実行したいSQL
$sql = 'SELECT * FROM User WHERE id=:id';  // Userテーブルの指定列を取得

//-------------------------------------------------
// SQLを実行
//-------------------------------------------------
try{
  $dbh = connectDB();
  $sth = query($dbh, $sql, [
            ['name'=>':id', 'value'=>$uid, 'type'=>PDO::PARAM_INT]
           ]);

  // 実行結果から1レコード取ってくる
  $buff = $sth->fetch(PDO::FETCH_ASSOC);
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
  sendResponse(false, 'Not Fund user');
}
// データを正常に取得
else{
  sendResponse(true, $buff);
}

