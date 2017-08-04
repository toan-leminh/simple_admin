<?php
include('menu.php');
date_default_timezone_set('Asia/Tokyo');
//$Path ='/etc/asterisk/cf/cal/';
$Path = 'cf/cal/';

$weekjp = array('日', '月', '火', '水', '木', '金', '土');
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    echo '<table>';
    echo '<th>日付</th><th>開始時間</th><th>終了時間</th><th> </th></tr>';
    for ($i = 0; $i < 7; $i++) {
        echo '<form action="cal.php" method="post"><tr>';
        echo '<td>' . date("m/d ", strtotime("+{$i} day"));
        $weekday = date("w", strtotime("+{$i} day"));
        echo '(' . $weekjp[$weekday] . ')</td>';
        echo '<td><input type=time size=5 name=open value="';
        echo substr(basename(glob($Path . "[" . $weekday . "].Time1-Open_[0-2][0-9]:[0-5][0-9]")[0]), 13);
        echo '"></td><td><input type=time size=5 name=close value="';
        echo substr(basename(glob($Path . "[" . $weekday . "].Time2-Close_[0-2][0-9]:[0-5][0-9]")[0]), 14);
        echo '"></td>';
        echo "<td><button type='submit' name='weekdayNo' value=" . $weekday . ">変更</button></td>";
        echo '</tr></form>';
    }
    echo '</table>';
} else {
    if (checkAuth() == 1) {
        //echo $_POST['weekdayNo']." ".$_POST['open']." ".$_POST['close']."<br />";     //test
        //--- open time ---
        $hour = substr($_POST['open'], 0, 2);
        $min = substr($_POST['open'], 3, 2);
        //echo $hour ."-" . $min;       //test
        if (checktime($hour, $min)) {
            //echo "open OK<br />"; /////test
            //echo $_POST['weekdayNo'] .".Time1-Open_";
            $file0 = glob($Path . $_POST['weekdayNo'] . ".Time1-Open_*")[0];
            $file1 = $Path . $_POST['weekdayNo'] . ".Time1-Open_" . $hour . ":" . $min;
            //echo $file0. $file1;  //test
            unlink($file0);
            file_put_contents($file1, "");
        } else {
            echo "error";
            exit;
        }
        // --- close time ---
        $hour = substr($_POST['close'], 0, 2);
        $min = substr($_POST['close'], 3, 2);
        //echo $hour ."-" . $min;       //test
        if (checktime($hour, $min)) {
            //echo "open OK<br />"; /////test
            //echo $_POST['weekdayNo'] .".Time1-Open_";
            $file0 = glob($Path . substr($_POST['weekdayNo'], 0, 1) . ".Time2-Close_*")[0];
            $file1 = $Path . substr($_POST['weekdayNo'], 0, 1) . ".Time2-Close_" . $hour . ":" . $min;
            //echo $file0. $file1;  //test
            unlink($file0);
            file_put_contents($file1, "");
        } else {
            echo "error.<a href='./cal.php'>戻る</a>";
            exit;
        }
        echo "<html>
      <head>
        <meta content='text/html; charset=UTF-8' http-equiv='Content-Type' />
        <title>Javascript?^CAEgt_CNgTv</title>
        <script type='text/javascript'>
        //<![CDATA[
          function redirect() {
            var x = 4;
            return function () {
              if(x > 0) {
                // console.log(x);
                document.getElementById('time').innerHTML = x;
                x--;
              } else {
                location.href='./cal.php';
              }
            }
          }
          var f = redirect();
          setInterval('f()', 1000);
        //]]>
        </script>
      </head>
      <body>
        <p>変更しました</p>
        <p><span id='time'>5</span>秒後にジャンプします</p>
        <p>自動的にジャンプしない場合は<a href='./cal.php'>こちら</a></p>
      </body>
    </html>";

    }else{
        echo "<b>管理者権限がありません</b><br />";
        echo "<a href='./cal.php'>戻る</a>";
    }
}
function checktime($hour, $min)
{
    if ($hour < 0 || $hour > 23 || !is_numeric($hour)) {
        return false;
    }
    if ($min < 0 || $min > 59 || !is_numeric($min)) {
        return false;
    }
    return true;
}

?>

