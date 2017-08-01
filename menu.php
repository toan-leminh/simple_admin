<?php
require_once "Auth.php";

// Set no cache on browser
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

if (isset($_POST['add_button'])) {
    //管理者権限を追加
    if(add()){
        $message = '権限を取得しました';
    }else{
        $message = '権限を取得失敗しました';
    }
} else if (isset($_POST['delete_button'])) {
    //管理者権限を削除
    if(delete()){
        $message = '権限を削除しました';
    }else{
        $message = '権限を削除失敗しました';
    };
} else if (isset($_POST['unlock_button'])) {
    //ファイルロックを削除
    unlock();
    $message = 'ファイルロックをクリアしました';
}

// 現状の管理者権限を確認
$auth = checkAuth();

?>
<html lang="jp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="style.css" type="text/css">
    <title>pbx 管理画面</title>
    <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>

<div class="menu" align="left">
    <ul>
        <li><a href=dial.php>内線番号管理</a></li>
        <li><a href=cal.php>営業時間管理</a></li>
        <li><a href=mode.php>営業時間外/非通知拒否 設定</a></li>
	<li><a href=ana.php>営業時間外アナウンス設定</a></li>
    </ul>

    <ul>
        <li><a href=iax.php>子機登録(IAX)</a></li>
        <li><a href=sip.php>外線登録(SIP)</a></li>
        <li><a href=vm.php>留守電メール登録</a></li>
        <li><a href=ban.php>着信拒否番号</a></li>
    </ul>
    <div class="privilege">
        <div class="display">
            <h2>権限: <?php echo $auth==1 ? '管理者' : 'なし' ?></h2>
            <span class="error"> <?php echo $message ?></span>
        </div>
        <hr>
        <form class="form-horizontal" method="post">
            <?php if($auth == 1){ ?>
                <input type="submit" class="btn btn-primary" id="delete_button"  name="delete_button" value="管理者権限を削除" />
            <?php }else{ ?>
                <input type="submit" class="btn btn-success"  id="add_button" name="add_button" value="管理者権限を取得" />
                <div style="height: 10px"></div>
                <input type="submit" class="btn btn-danger"  id="unlock_button"  name="unlock_button" value="ロックファイルをクリア" />
            <?php } ?>
        </form>
    </div>
</div>
<script>
    $('#add_button').on('click', function (e) {
        // メッセージ表示
        if(!confirm('管理者権限を取得しますか？')){
            e.preventDefault();
        }
    });

    $('#delete_button').on('click', function (e) {
        // メッセージ表示
        if(!confirm('管理者権限を削除しますか？')){
            e.preventDefault();
        }
    });

    $('#unlock_button').on('click', function (e) {
        // メッセージ表示
        if(!confirm('他が制御権を持っていますが、強制取得しますか？')){
            e.preventDefault();
        }
    });
</script>

<div class="main" align="left"> <br/>

