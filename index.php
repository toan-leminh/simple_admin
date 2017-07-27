<?php
/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 7/26/17
 * Time: 13:45
 */
require_once "Auth.php";

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
<html>
<head>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>
<div class="container">
    <h2>権限: <?php echo $auth==1 ? '管理者' : 'なし' ?></h2>
    <span class="error"> <?php echo $message ?></span>
    <hr>
    <form class="form-horizontal" method="post">
        <div class="form-group text-center">
            <input type="submit" class="btn btn-success" id="add_button" name="add_button" value="管理者権限を取得" />
            <input type="submit" class="btn btn-primary" id="delete_button"  name="delete_button" value="管理者権限を削除" />
            <input type="submit" class="btn btn-danger" id="delete_button"  name="unlock_button" value="ロックファイルをクリア" />
        </div>
    </form>
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
</script>
</body>
</html>
