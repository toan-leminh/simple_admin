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
    add();
} else if (isset($_POST['delete_button'])) {
    //管理者権限を削除
    delete();
}

// 現状の管理者権限を確認
$auth = checkAuth();

/**
 * 管理者権限を取得
 */
function add(){
    // 管理者権限を確認する。権限がない場合追加する
    if(!checkAuth()){
        $content = AUTH_KEY .  ':' . session_id();
        file_put_contents(AUTH_PATH_FILE, $content);
    }
}

/**
 * 管理者権限を削除
 */
function delete(){
    // 管理者権限を確認する。権限がある場合削除する
    if(checkAuth()){
        file_put_contents(AUTH_PATH_FILE, '');
    }
}

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
