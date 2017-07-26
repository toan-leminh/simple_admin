<?php
/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 7/26/17
 * Time: 13:45
 */
require_once "Auth.php";

// セッションが存在しない場合、開始する
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['add_button'])) {
    //管理者権限を追加
    Auth::add();
} else if (isset($_POST['delete_button'])) {
    //管理者権限を削除
    Auth::delete();
}

// 現状の管理者権限を確認
$auth = Auth::check();

?>
<html>
<head>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>
<div class="container">
    <h2>権限: <?php echo $auth ? '管理者' : 'なし' ?></h2>
    <hr>
    <form class="form-horizontal" method="post">
        <div class="form-group text-center">
            <input type="submit" class="btn btn-success" id="add_button" name="add_button" value="管理者権限を取得" />
            <input type="submit" class="btn btn-danger" id="delete_button"  name="delete_button" value="管理者権限を削除" />
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
