<?php
/**
 * MySQLに接続しデータを追加する
 *
 */

// 以下のコメントを外すと実行時エラーが発生した際にエラー内容が表示される
// ini_set('display_errors', 'On');
// ini_set('error_reporting', E_ALL);

//-------------------------------------------------
// ライブラリ
//-------------------------------------------------
require('../util.php');

//-------------------------------------------------
// 初期値
//-------------------------------------------------
define('DEFAULT_LV', 1);
define('DEFAULT_EXP', 1);
define('DEFAULT_MONEY', 3000);

//-------------------------------------------------
// 準備
//-------------------------------------------------
// 実行したいSQL
$sql1 = 'INSERT INTO User(lv, exp, money) VALUES(:lv, :exp, :money)';
$sql2 = 'SELECT LAST_INSERT_ID() as id';  // AUTO INCREMENTした値を取得する

//-------------------------------------------------
// SQLを実行
//-------------------------------------------------
try{
  $dbh = connectDB();

  //-------------------------------------------------
  // 新規にレコードを作成
  //-------------------------------------------------
  $sth = query($dbh, $sql1, [
            ['name'=>':lv',    'value'=>DEFAULT_LV,    'type'=>PDO::PARAM_INT],
            ['name'=>':exp',   'value'=>DEFAULT_EXP,   'type'=>PDO::PARAM_INT],
            ['name'=>':money', 'value'=>DEFAULT_MONEY, 'type'=>PDO::PARAM_INT]
           ]);

  //-------------------------------------------------
  // AUTO INCREMENTした値を取得
  //-------------------------------------------------
  $sth = query($dbh, $sql2);

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
  sendResponse(false, 'Database error: can not fetch LAST_INSERT_ID()');
}
// データを正常に取得
else{
  sendResponse(true, $buff['id']);
}

