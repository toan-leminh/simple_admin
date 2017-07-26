<?php
/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 7/26/17
 * Time: 21:36
 */
require_once "Auth.php";

$auth = checkAuth();

?>

<html>
<head>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
</head>
<body>
<div class="container">
    <h2>権限: <?php echo $auth ? '管理者' : 'なし' ?></h2>
</div>
</body>
</html>

