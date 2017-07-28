<?php
include('menu.php');
require_once 'Auth.php';

//$path='/etc/asterisk/cf/cal/';
$path='cal/';


if(isset($_GET['mode'])) {
    if( $_GET['mode']=='off'){
        //echo 'offff';
        if(checkAuth() == 1){
            if(rename( $path.'mode-on' ,$path.'mode-off')){ echo "<b>offに変更しました</b><br />";}
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
}
if(isset($_GET['mode'])){
    if( $_GET['mode']=='on'){
        //echo 'offff';
        if(checkAuth() == 1){
            if(rename( $path.'mode-off' ,$path.'mode-on')){ echo "<b>onに変更しました</b><br />";}
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
}

if(is_file($path.'mode-on')){
    echo '営業時間外 アナウンスON ';
    echo '<a href=mode.php?mode=off>offに変更</a>';
}else{
    echo '非通知拒否 アナウンスOFF ';
    echo '<a href=mode.php?mode=on>onに変更</a>';
}

echo '<hr>';
if(isset($_GET['ano'])){
    if($_GET['ano']=='off'){
        //echo 'offff';
        if(checkAuth() == 1) {
            if(rename( $path.'anonymous-on' ,$path.'anonymous-off')){ echo "<b>offに変更しました</b><br />";}
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
}

if(isset($_GET['ano'])){
    if( $_GET['ano']=='on'){
        //echo 'offff';
        if(checkAuth() == 1) {
            if(rename( $path.'anonymous-off' ,$path.'anonymous-on')){ echo "<b>onに変更しました</b><br />";}
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
}

if(is_file($path.'anonymous-on')){
    echo '非通知拒否 ON ';
    echo '<a href=mode.php?ano=off>offに変更</a>';
}else{
    echo '非通知拒否 OFF ';
    echo '<a href=mode.php?ano=on>onに変更</a>';
}