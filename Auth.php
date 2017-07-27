<?php
/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 7/26/17
 * Time: 13:45
 */

define('AUTH_PATH_FILE', '.auth');
define('AUTH_KEY', 'ADMIN');

// セッションが存在しない場合、開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * 管理者権限を確認
 * @return bool TRUE:権限あり FALSE:権限なし
 */
function checkAuth(){
    $checkAuth = false;

    // .authファイルを読み取り
    $authString = readAuthFile();
    $authArray = explode(':', $authString);

    // ファイル内容にADMINのセッションIDを確認する
    if($authArray && count($authArray) == 2 && $authArray[0] == AUTH_KEY && $authArray[1] == session_id()){
        $checkAuth = true;
    }
    return $checkAuth;
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
