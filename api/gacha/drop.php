<?php
/**
 * ガチャAPI
 *
 */

// 以下のコメントを外すと実行時エラーが発生した際にエラー内容が表示される
// ini_set('display_errors', 'On');
// ini_set('error_reporting', E_ALL);

//-------------------------------------------------
// ライブラリ
//-------------------------------------------------
require_once('../util.php');
require_once('../../model/gacha.php');
require_once('../../model/user.php');

//-------------------------------------------------
// 定数
//-------------------------------------------------
// キャラクター数
define('MAX_CHARA', 10);

// ガチゃ1回の価格
define('GACHA_PRICE', 300);


//-------------------------------------------------
// 引数を受け取る
//-------------------------------------------------
$uid = UserModel::getUserIDfromQuery();

if( !$uid ){
  sendResponse(false, 'Invalid uid');
  exit(1);
}

//-------------------------------------------------
// 準備
//-------------------------------------------------
//---------------------------
// 実行したいSQL
//---------------------------
// Userテーブルから所持金を取得
$sql1 = 'SELECT money FROM User WHERE id=:userid';

// Userテーブルの所持金を減産
$sql2 = 'UPDATE User SET money=money-:price WHERE id=:userid';

// UserCharaテーブルにキャラクターを追加
$sql3 = 'INSERT INTO UserChara(user_id, chara_id) VALUES(:userid,:charaid)';

// Charaテーブルから1レコード取得
$sql4 = 'SELECT * FROM Chara WHERE id=:charaid';


//-------------------------------------------------
// SQLを実行
//-------------------------------------------------
try{
  $dbh = connectDB();
  
  // トランザクション開始
  $dbh->beginTransaction();

  //---------------------------
  // 所持金の残高を取得
  //---------------------------
  $sth = query($dbh, $sql1, [
            ['name'=>':userid', 'value'=>$uid, 'type'=>PDO::PARAM_INT]
           ]);
  $buff = $sth->fetch(PDO::FETCH_ASSOC);

  if( $buff === false ){
    sendResponse(false, 'Not Found User');
    exit(1);
  }
  
  // 残高が足りているかチェック
  if( $buff['money'] < GACHA_PRICE ){
    sendResponse(false, 'The balance is not enough');
    exit(1);
  }

  //---------------------------
  // 残高を減らす
  //---------------------------
  $sth = query($dbh, $sql2, [
            ['name'=>':price',  'value'=>GACHA_PRICE, 'type'=>PDO::PARAM_INT],
            ['name'=>':userid', 'value'=>$uid,        'type'=>PDO::PARAM_INT]
           ]);

  //---------------------------
  // キャラクターを抽選
  //---------------------------
  $charaid = random_int(1, MAX_CHARA);

  //---------------------------
  // キャラクターを所有
  //---------------------------
  $sth = query($dbh, $sql3, [
            ['name'=>':userid',  'value'=>$uid,     'type'=>PDO::PARAM_INT],
            ['name'=>':charaid', 'value'=>$charaid, 'type'=>PDO::PARAM_INT]
           ]);
  
  //---------------------------
  // キャラクター情報を取得
  //---------------------------
  $sth = query($dbh, $sql4, [
            ['name'=>':charaid', 'value'=>$charaid, 'type'=>PDO::PARAM_INT]
           ]);
  $chara = $sth->fetch(PDO::FETCH_ASSOC);

  //---------------------------
  // トランザクション確定
  //---------------------------
  $dbh->commit();
}
catch( PDOException $e ) {
  // ロールバック
  $dbh->rollBack();

  sendResponse(false, 'Database error: '.$e->getMessage());  // 本来エラーメッセージはサーバ内のログへ保存する(悪意のある人間にヒントを与えない)
  exit(1);
}

//-------------------------------------------------
// 実行結果を返却
//-------------------------------------------------
// データが0件
if( $buff === false ){
  sendResponse(false, 'System Error');
}
// データを正常に取得
else{
  sendResponse(true, $chara);
}

