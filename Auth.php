<?php
/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 7/26/17
 * Time: 13:45
 */

define('AUTH_PATH_FILE', 'cf/.auth');
define('AUTH_KEY', 'ADMIN');

// セッションが存在しない場合、開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * 管理者権限を確認
 * @return bool 0:権限なし、取得可能 1:権限あり、 -1: 権限なし、取得不可（強制取得が必要）
 */
function checkAuth(){
    $checkAuth = 0; // 権限がない、取得可能

    // .authファイルを読み取り
    $authString = readAuthFile();
    $authArray = explode(':', $authString);

    // ファイル内容にADMINのセッションIDを確認する
    if($authString){
        if($authArray && count($authArray) == 2 && $authArray[0] == AUTH_KEY && $authArray[1] == session_id()){
            $checkAuth = 1;
        }else{
            $checkAuth = -1;
        }
    }
    return $checkAuth;
}


/**
 * 管理者権限を取得
 */
function add(){
    $authResult = checkAuth();
    // すでに管理者権限をもつ
    if($authResult == 1){
        return true;
    }

    // 管理者権限を確認する。権限がない場合追加する
    if($authResult == 0){
        $content = AUTH_KEY .  ':' . session_id();

        file_put_contents(AUTH_PATH_FILE, $content);
        return true;
    }
     return false;
}

/**
 * 管理者権限を削除
 */
function delete(){
    // 管理者権限を確認する。権限がある場合削除する
    if(checkAuth() == 1){
        file_put_contents(AUTH_PATH_FILE, '');
        return true;
    }
    return false;
}

/**
 * .authFileを強制にunlockする
 */
function unlock(){
    file_put_contents(AUTH_PATH_FILE, '');
}

/**
 * .authFile の内容を読み込む
 * @return null|string
 */
function readAuthFile(){
    if(file_exists(AUTH_PATH_FILE)){
        return file_get_contents(AUTH_PATH_FILE);
    }else{
        return null;
    }
}
