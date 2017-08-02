<?php
include('menu.php');
define('MAX_PHONE_NO', 10);  // 電話番号入力行数

$Path = 'ban/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['phone']){
        $phone = $_POST['phone'];
        $originalPhone = $_POST['original_phone'];
        if (phoneChk($phone)) {
            if (checkAuth() == 1) {
                if (is_file($Path . $originalPhone)) {
                    unlink($Path . $originalPhone);
                }
                if (!file_put_contents($Path . $phone , "")) {
                    echo "着信拒否番号" . $phone . "を更新しました";
                }
            }else {
                echo "<b>管理者権限がありません</b><br />";
            }
        } else {
            echo "電話番号が正しくないようです";
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['phone']) && $_GET['phone']) {
        if(phoneChk($_GET['phone'])){
            $phone = $_GET['phone'];
            if (is_file($Path . $phone )) {
                if (checkAuth() == 1) {
                    if (unlink($Path . $phone )) {
                        echo "着信拒否番号" . $phone . "を削除しました";
                    }
                }else{
                    echo "<b>管理者権限がありません</b><br />";
                }
            }
        } else {
            echo "電話番号が正しくないようです";
        }

    }
}
echo '<table>';
echo '<th>no</th><th>電話番号</th><th></th><th></th></tr>';

$phoneList = glob($Path . '*') ;
for ($i = 0; $i < MAX_PHONE_NO; $i++) {
    echo "<tr><form method='POST' action='ban.php'>";
    echo '<td>' . ($i+1)  . '</td><td>';
    $file = (($Path . $i ));
    if (isset($phoneList[$i])) {
        $value = basename($phoneList[$i]);
    } else {
        $value = "";
    }
    echo '<input type=text name=phone size=50 value="' . $value . '">';
    echo '<input type=hidden name=original_phone size=50 value="' . $value . '">';
    echo "</td><td><button type='submit' name='id' value=" . $value . ">edit</button></td></form>";
    echo "</td><form method='GET' action='ban.php'><td>";
    echo "<button type='submit' name='phone' value=" . $value . ">del</button></td></form>";
    echo "</form></tr>";
}
echo "</table>";

// 電話番号のバリデーション
function phoneChk($phone)
{
    return preg_match("/^[0-9]{0,15}$/", $phone);
}

?>

