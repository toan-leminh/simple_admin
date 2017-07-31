<?php
include('menu.php');

//$Path ='/etc/asterisk/cf/VOICE/';
$Path = 'VOICE/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //file_put_contents
    //echo grChk($_POST['id']).$_POST['mail']."<br>";       //test
    $id = grChk($_POST['id']);
    $mail = $_POST['mail'];
    if (mailChk($mail)) {
        if (is_file($Path . $id . "00")) {
            if (checkAuth() == 1) {
                unlink($Path . $id . "00");
            }else{
                echo "<b>管理者権限がありません</b><br />";
            }
        }
        if (checkAuth() == 1) {
            if (file_put_contents($Path . $id . "00", $id . "00" . "=>1234,demo," . $mail . ",,attach=yes|tz=japan|delete=yes")) {
                echo "代表留守電" . $id . "00番を更新しました。<br />留守番電話にメールが届くことをテストして下さい。";
            }
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    } else {
        echo "メールアドレスが正しくないようです";
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = grChk($_GET['id']);
        if (is_file($Path . $id . "00")) {
            if (checkAuth() == 1) {
                if (unlink($Path . $id . "00")) {
                    echo "代表留守電" . $id . "00番を削除しました";
                }
            }else{
                echo "<b>管理者権限がありません</b><br />";
            }
        }
    }
}
echo '<table>';
echo '<th>no</th><th>mail</th><th></th><th></th></tr>';
for ($i = 1; $i < 10; $i++) {
    echo "<tr><form method='POST' action='vm.php'>";
    echo '<td>' . $i * 100 . '</td><td>';
    $file = (($Path . $i * 100));
    if (is_file($file)) {
        $value = file_get_contents($file);
        $array = explode(',', $value);
        $value = $array[2];
    } else {
        $value = "";
    }
    echo '<input type=text name=mail size=50 value="' . $value . '">';
    echo "</td><td><button type='submit' name='id' value=" . $i . ">edit</button></td></form>";
    echo "</td><form method='GET' action='vm.php'><td>";
    echo "<button type='submit' name='id' value=" . $i . ">del</button></td></form>";
    echo "</form></tr>";
}
echo "</table>";
function mailChk($email)
{
    return false !== filter_var($email, FILTER_VALIDATE_EMAIL) && ']' !== substr($email, -1);
}

function grChk($gr)
{
    switch ($gr) {    //vs xss etc.
        case '1':
            return '1';
            break;
        case '2':
            return '2';
            break;
        case '3':
            return '3';
            break;
        case '4':
            return '4';
            break;
        case '5':
            return '5';
            break;
        case '6':
            return '6';
            break;
        case '7':
            return '7';
            break;
        case '8':
            return '8';
            break;
        case '9':
            return '9';
            break;
        default:
            return '1';
            break;
    }
}

?>

