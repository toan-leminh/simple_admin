<?php
include('menu.php');

echo '<table>';

//$Path ='/etc/asterisk/cf/main/';
$Path = 'cf/main/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $daihyoNo = checkDaihyoNo($_POST['daihyoNo']);
    $originalDaihyoNo = checkDaihyoNo($_POST['original_daihyoNo']);

    if (isset($_POST['user1'])) {
        $file = substr($_POST['user1'], 0, 3);
        $Num = grChk($_POST['Num']);

        if (checkAuth() == 1) {
            // 代表番号変更の場合
            if($daihyoNo != $originalDaihyoNo){
                // Remove original file
                $originalFilePath = $Path . $Num . '/t1_' . $originalDaihyoNo;
                if (is_file($originalFilePath)) {
                    unlink($originalFilePath);
                }

                // Create new file
                $filePath = $Path . $Num . '/t1_' . $daihyoNo;
                if (!file_put_contents($filePath , "")) {
                    echo "代表" . ($Num * 100) . "欄の代表1の待機時間を更新しました";
                }
            // 内線追加の場合
            }else{
                if (!file_put_contents($Path . $Num . '/m1/' . $file, "")) {
                    echo "内線[" . $file . "]を代表電番" . $Num . "00の代表1に追加しました";
                }
            }

        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
    if (isset($_POST['user2'])) {
        $file = substr($_POST['user2'], 0, 3);
        $Num = grChk($_POST['Num']);

        if (checkAuth() == 1) {
            // 代表番号変更の場合
            if($daihyoNo != $originalDaihyoNo){
                // Remove original file
                $originalFilePath = $Path . $Num . '/t2_' . $originalDaihyoNo;
                if (is_file($originalFilePath)) {
                    unlink($originalFilePath);
                }

                // Create new file
                $filePath = $Path . $Num . '/t2_' . $daihyoNo;
                if (!file_put_contents($filePath , "")) {
                    echo "代表" . ($Num * 100) . "欄の代表2の待機時間を更新しました";
                }
            }else{
                if (!file_put_contents($Path . $Num . '/m2/' . $file, "")) {
                    echo "内線[" . $file . "]を代表電番" . $Num . "00の代表2に追加しました";
                }
            }
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
}
if (isset($_GET['FP'])) {
    $FP = substr($_GET['FP'], 0, 8);
    if (!preg_match('/^[1-9]\/m[1-2]\/[1-9][0-9][0-9]/', $FP)) {
        $FP = "100";
    }
    $file = $Path . $FP;
    if (file_exists($file)) {
        if (checkAuth() == 1) {
            if (unlink($file)) {
                echo basename($file) . "を除外しました";
            }
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
}
if (isset($_GET['FPon'])) {
    $file = $Path . grChk($_GET['FPon']) . "/mail/on";
    if (file_exists($file)) {
        if (checkAuth() == 1) {
            if (unlink($file)) {
                echo "代表電番" . grChk($_GET['FPon']) . "00の留守電を解除しました";
            }
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }
    }
}
if (isset($_GET['FPoff'])) {
    $file = $Path . grChk($_GET['FPoff']) . "/mail/on";
    if (checkAuth() == 1) {
        if (!file_put_contents($file, "")) {
            echo "代表電番" . grChk($_GET['FPoff']) . "00の留守電を設定しました";
        }
    }else{
        echo "<b>管理者権限がありません</b><br />";
    }
}
echo '<tr><th>代表電番</th><th>種類</th><th>種類</th><th>ステータス</th><th>変更</th><th> </th></tr>';
for ($i = 1; $i < 10; $i++) {
    // 代表番号を取得
    $fileList = glob($Path . $i . '/*');
    $daihyoNo = [];
    foreach ($fileList as $file){
        // Use pattern to get daihyoNo
        preg_match('/t(?P<no>\d{1})_(?P<daihyoNo>\d+)/', basename($file), $matches);
        if($matches){
            $daihyoNo[$matches['no']] = $matches['daihyoNo'];
        }
    }

    echo '<tr>';
    echo '<td rowspan=4>' . $i * 100 . '</td>';
    echo '<tr><form method=post action=dial.php><td>代表1';
    echo '<select name="daihyoNo" class="daihyoNo">';
    for ($k = 10; $k <= 99; $k++) {
        $selected = ($k == $daihyoNo[1]) ? 'selected' : '';
        echo "<option value='{$k}' {$selected}>{$k}</option>";
    }
    echo '</select>';
    echo '</td><td>';

    $opt = "";
    for ($j = 101; $j < 1000; $j++) {
        if (substr($j, 1, 2) !== '00') {
            $opt .= "<option value='{$j}'>{$j}</option>";
        }
    }
    foreach (glob($Path . $i . '/m1/*') as $file) {
        if (is_file($file)) {
            echo "<a href=dial.php?FP=" . str_replace($Path, "", $file) . ">";
            echo basename($file) . "</a> ";
            $opt = str_replace("<option value='" . basename($file) . "'>" . basename($file) . "</option>", "", $opt);
        }
    }
    echo '</td><td><select name=user1>';
    echo $opt;
    echo '</select><button type=submit >追加</button>';
    echo "<input type='hidden' name='original_daihyoNo' class='daihyoNo' value='{$daihyoNo[1]}'>";
    echo "<input type='hidden' name='Num' value='{$i}'>";
    echo '</td></form></tr><tr><form method=post action=dial.php><td>代表2';
    echo '<select name="daihyoNo" class="daihyoNo">';
    for ($k = 10; $k <= 99; $k++) {
        $selected = ($k == $daihyoNo[2]) ? 'selected' : '';
        echo "<option value='{$k}' {$selected}>{$k}</option>";
    }
    echo '</select>';
    echo '</td><td>';
    $opt = "";
    for ($j = 101; $j < 1000; $j++) {
        if (substr($j, 1, 2) !== '00') {
            $opt .= "<option value='{$j}'>{$j}</option>";
        }
    }
    foreach (glob($Path . $i . '/m2/*') as $file) {
        if (is_file($file)) {
            echo "<a href=dial.php?FP=" . str_replace($Path, "", $file) . ">";
            echo basename($file) . "</a> ";
            $opt = str_replace("<option value='" . basename($file) . "'>" . basename($file) . "</option>", "", $opt);
        }
    }
    echo '</td><td><select name=user2>';
    echo $opt;
    echo '</select><button type=submit >追加</button>';
    echo "<input type='hidden' name='original_daihyoNo' class='daihyoNo' value='{$daihyoNo[2]}'>";
    echo "<input type='hidden' name='Num' value='{$i}'>";
    echo '</td></from></tr><tr><td>留守電</td><td>';
    $mailpath = $Path . $i . '/mail/*';
    $mail = "";
    $mail2 = "";
    if (isset(glob($mailpath)[0])) {
        $mail = glob($mailpath)[0];
    }
    if (!empty($mail)) {      //on
        echo "<a href=dial.php?FPon={$i}>on</a>";
    } else {
        $mail2 = "<a href=dial.php?FPoff={$i}>off(onへ変更)</a>";
        echo '</td><td>';
    }
    echo $mail2 . '</td><td>';
    echo '</td></form></tr>';
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

// Validate daihyoNo
function checkDaihyoNo($daihyoNo){
    if( $daihyoNo && is_numeric($daihyoNo) && ($daihyoNo >= 10) && ($daihyoNo <= 99)){
        return $daihyoNo;
    }else{
        return 10;
    }
}

?>
<script>
    $(function() {
        // 代表番号変更のイベント
        $('.daihyoNo').on('change', function () {
            var $this = $(this);
            if(confirm('待機時間の変更をしますか？')){
                // Submit form
                $this.closest('tr').find('button:submit').click();
            }else{
                // Revert data change from hidden field
                $this.val($this.closest('tr').find('input:hidden.daihyoNo').val());
            }
        })
    });
</script>

