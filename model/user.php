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

  /**
   * 所持金を返却
   *
   * @param integer $uid
   * @return integer|false
   */
  function getMoney($uid){
    $buff = $this->getRecordById($uid);
    if( $buff !== false ){
      return($buff['money']);
    }
    else{
      return(false);
    }
  }

  /**
   * 所持金を利用する（減らす）
   *
   * @param integer $uid
   * @param integer $value
   * @param boolean [$safety=true]
   * @return boolean 
   */
  function useMoney($uid, $value, $safety=true){
    // 残高がマイナスにならないかチェック
    if( $safety ){
      $money = $this->getMoney($uid);
      if( ($money === false) || ($money-$value) < 0 ){
         $this->setError('The balance is not enough');
        return(false);
      }
    }

    // 残高を減らす
    $sql  = 'UPDATE User SET money=money-:price WHERE id=:userid';
    $bind = [
    	['name'=>':price',  'value'=>$value, 'type'=>PDO::PARAM_INT],
    	['name'=>':userid', 'value'=>$uid,   'type'=>PDO::PARAM_INT]
     ];

    return( $this->query($sql, $bind) );
  }

  /**
   * キャラクターを所有する
   *
   * @param integer $uid
   * @param integer $charaid
   * @return boolean
   */
  function addChara($uid, $charaid){
    $sql  = 'INSERT INTO UserChara(user_id, chara_id) VALUES(:userid,:charaid)';
    $bind = [
      ['name'=>':userid',  'value'=>$uid,     'type'=>PDO::PARAM_INT],
      ['name'=>':charaid', 'value'=>$charaid, 'type'=>PDO::PARAM_INT]
    ];

    return( $this->query($sql, $bind) );
  }

}