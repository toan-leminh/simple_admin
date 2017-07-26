<?php

/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 7/26/17
 * Time: 13:45
 */
class Auth
{
    const AUTH_PATH_FILE = '.auth';
    const AUTH_KEY = 'ADMIN';

    /**
     * 管理者権限を確認
     * @return bool TRUE:権限あり FALSE:権限なし
     */
    public static function check(){
        $checkAuth = false;

        $authString = self::readAuthFile();
        if($authString == self::AUTH_KEY){
            $checkAuth = true;
        }
        return $checkAuth;
    }

    /**
     * 管理者権限を取得
     */
    public static function add(){
        // 管理者権限を確認する。権限がない場合追加する
        if(!self::check()){
            file_put_contents(self::AUTH_PATH_FILE, self::AUTH_KEY);
        }
    }

    /**
     * 管理者権限を削除
     */
    public static function delete(){
        // 管理者権限を確認する。権限がある場合削除する
        if(self::check()){
            file_put_contents(self::AUTH_PATH_FILE, '');
        }
    }

    /**
     * .authFile の内容を読み込む
     * @return null|string
     */
    public static function readAuthFile(){
        if(file_exists(self::AUTH_PATH_FILE)){
            return file_get_contents(self::AUTH_PATH_FILE);
        }else{
            return null;
        }
    }
}