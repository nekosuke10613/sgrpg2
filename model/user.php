<?php
require_once('model.php');

/**
 * Userモデル
 *
 * @version 1.0.0
 * @author M.Katsube <katsubemakito@gmail.com>
 */
class UserModel extends Model{
  // 対象テーブル
  protected $tableName = 'User';

  // レコードの初期値
  private $defaultValue = [
    ['name'=>':lv',    'value'=>1,    'type'=>PDO::PARAM_INT],
    ['name'=>':exp',   'value'=>1,    'type'=>PDO::PARAM_INT],
    ['name'=>':money', 'value'=>3000, 'type'=>PDO::PARAM_INT]
  ];

  /**
   * UserIDの書式が正しいかチェック
    *
   * @return integer|false
    */
  static function getUserIDfromQuery(){
    $uid = isset($_GET['uid'])?  $_GET['uid']:null;

    if( ($uid === null) || (!is_numeric($uid)) ){
      return(false);
    }
    else{
      return($uid);
    }
  }

  /**
    * ユーザーを追加
    *
   * @return integer|false
    */
  function join(){
    // ユーザーを追加
    $sql1 = 'INSERT INTO User(lv, exp, money) VALUES(:lv, :exp, :money)';
    $this->query($sql1, $this->defaultValue);

    // AUTO_INCREMENTしたユーザーIDを取得
    $sql2 = 'SELECT LAST_INSERT_ID() as id';
    $this->query($sql2);
    $buff = $this->fetch();
    
    return( $buff['id'] );
  }
}

