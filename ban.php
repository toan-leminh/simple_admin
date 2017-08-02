<?php
include('menu.php');
define('MAX_PHONE_NO', 10);  // 電話番号入力行数

$Path = 'ban/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //file_put_contents
    //echo grChk($_POST['id']).$_POST['mail']."<br>";       //test
    $id = grChk($_POST['id']);
    $phone = $_POST['phone'];
    if (phoneChk($phone)) {
        if (is_file($Path . $id)) {
            if (checkAuth() == 1) {
                unlink($Path . $id);
            }else{
                echo "<b>管理者権限がありません</b><br />";
            }
        }
        if (checkAuth() == 1) {
            if (file_put_contents($Path . $id , $id  . "=>1234,demo," . $phone . ",,attach=yes|tz=japan|delete=yes")) {
                echo "着信拒否番号" . $id . "番を更新しました";
            }
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    } else {
        echo "電話番号が正しくないようです";
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = grChk($_GET['id']);
        if (is_file($Path . $id )) {
            if (checkAuth() == 1) {
                if (unlink($Path . $id )) {
                    echo "着信拒否番号" . $id . "番を削除しました";
                }
            }else{
                echo "<b>管理者権限がありません</b><br />";
            }
        }
    }
}
echo '<table>';
echo '<th>no</th><th>電話番号</th><th></th><th></th></tr>';
for ($i = 1; $i <= MAX_PHONE_NO; $i++) {
    echo "<tr><form method='POST' action='ban.php'>";
    echo '<td>' . $i  . '</td><td>';
    $file = (($Path . $i ));
    if (is_file($file)) {
        $value = file_get_contents($file);
        $array = explode(',', $value);
        $value = $array[2];
    } else {
        $value = "";
    }
    echo '<input type=text name=phone size=50 value="' . $value . '">';
    echo "</td><td><button type='submit' name='id' value=" . $i . ">edit</button></td></form>";
    echo "</td><form method='GET' action='ban.php'><td>";
    echo "<button type='submit' name='id' value=" . $i . ">del</button></td></form>";
    echo "</form></tr>";
}
echo "</table>";

// 電話番号のバリデーション
function phoneChk($phone)
{
    return preg_match("/^[0-9]{0,15}$/", $phone);
}

// 行目のバリデーション
function grChk($gr)
{
    if( false !== filter_var($gr, FILTER_VALIDATE_INT) && $gr <= MAX_PHONE_NO && $gr >= 1){
        return $gr . '';
    }else{
        return '1';
    }
}

?>

