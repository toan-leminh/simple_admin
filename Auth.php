<?php

/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 7/26/17
 * Time: 13:45
 */
class Auth
{
    /**
     * 管理者権限を確認
     * @return bool TRUE:権限あり FALSE:権限なし
     */
    public static function check(){
        $checkAuth = false;

        if($_SESSION && isset($_SESSION['Auth']) && $_SESSION['Auth'] == 'ADMIN'){
            $checkAuth = true;
        }
        return $checkAuth;
    }

    /**
     * 管理者権限を取得
     */
    public static function add(){
        // セッション開始
        if(!$_SESSION){
            session_start();
        }

        // 管理者権限を確認する。権限がない場合追加する
        if(!self::check()){
            $_SESSION['Auth'] = 'ADMIN';
        }
    }

    /**
     * 管理者権限を削除
     */
    public static function delete(){
        // 管理者権限を確認する。権限がある場合削除する
        if(self::check()){
            unset($_SESSION["Auth"]);
        }
    }
}