<?php
include('menu.php');
define('MIN_ROOM_NO', 30);
define('MAX_ROOM_NO', 45);


$Path = 'cf/meetme/';
$filePrefix = 'ConfBridgePass';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['room_no']){
        $roomNo = $_POST['room_no'];
        $password = $_POST['password'];
        $originalPassword = $_POST['original_password'];

        if($password){
            if (passwordCheck($password)) {
                if (checkAuth() == 1) {
                    // Remove original file
                    $originalFilePath = $Path . $filePrefix . $roomNo. '_' . $originalPassword;
                    if (is_file($originalFilePath)) {
                        unlink($originalFilePath);
                    }

                    // Create new file
                    $filePath = $Path . $filePrefix . $roomNo. '_' . $password;
                    if (!file_put_contents($filePath , "")) {
                        echo "会議番号" . $roomNo . "のパスワードを更新しました";
                    }
                }else {
                    echo "<b>管理者権限がありません</b><br />";
                }
            } else {
                echo "パスワードが正しくないようです";
            }
        }else{
            echo "パスワードを入力してください";
        }

    }
}

echo '<table>';
echo '<th>会議番号</th><th>パスワード</th><th></th></tr>';

// Get room list in folder
$fileList = glob($Path . '*') ;
$roomList = [];
foreach ($fileList as $file){
    // User sub-pattern to find roomNo and password
    preg_match('/\w+(?P<roomNo>\d{2})_(?P<password>\d+)/', basename($file), $matches);
    $roomList[$matches['roomNo']] = $matches['password'];
}


for ($i = MIN_ROOM_NO; $i <= MAX_ROOM_NO; $i++) {
    echo "<tr><form method='POST' action='meetme.php'>";
    echo '<td>' . $i  . '</td><td>';
    if (isset($roomList[$i])) {
        $value = $roomList[$i];
    } else {
        $value = "";
    }
    echo '<input type=text name=password size=50 value="' . $value . '">';
    echo '<input type=hidden name=original_password size=50 value="' . $value . '">';
    echo "</td><td><button type='submit' name='room_no' value=" . $i . ">edit</button></td></form>";
    echo "</td></tr>";
}
echo "</table>";

// パスワードのバリデーション
function passwordCheck($password)
{
    return preg_match("/^[0-9]{4}$/", $password);
}

?>

